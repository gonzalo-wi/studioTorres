<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Barber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarberPanelController extends Controller
{
    /**
     * Get stats for the authenticated barber
     */
    public function stats(Request $request)
    {
        $user = $request->user();
        
        // Verificar que el usuario sea un barbero
        $barber = Barber::where('user_id', $user->id)->first();
        
        if (!$barber) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró perfil de barbero'
            ], 403);
        }

        $today = now()->startOfDay();
        $startOfMonth = now()->startOfMonth();

        // Stats de hoy
        $todayAppointments = Appointment::where('barber_id', $barber->id)
            ->whereDate('starts_at', $today)
            ->count();

        // Stats del mes actual
        $monthAppointments = Appointment::where('barber_id', $barber->id)
            ->whereDate('starts_at', '>=', $startOfMonth)
            ->count();

        // Turnos pendientes (futuros)
        $pendingAppointments = Appointment::where('barber_id', $barber->id)
            ->where('starts_at', '>', now())
            ->where('status', 'PENDING')
            ->count();

        // Turnos confirmados hoy
        $confirmedToday = Appointment::where('barber_id', $barber->id)
            ->whereDate('starts_at', $today)
            ->where('status', 'CONFIRMED')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'todayAppointments' => $todayAppointments,
                'monthAppointments' => $monthAppointments,
                'pendingAppointments' => $pendingAppointments,
                'confirmedToday' => $confirmedToday
            ]
        ]);
    }

    /**
     * Get appointments for the authenticated barber
     */
    public function appointments(Request $request)
    {
        $user = $request->user();
        
        // Verificar que el usuario sea un barbero
        $barber = Barber::where('user_id', $user->id)->first();
        
        if (!$barber) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró perfil de barbero'
            ], 403);
        }

        $query = Appointment::where('barber_id', $barber->id)
            ->with(['service', 'barber']);

        // Filtros opcionales
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from')) {
            $query->whereDate('starts_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('starts_at', '<=', $request->date_to);
        }

        // Ordenar por fecha más reciente primero
        $appointments = $query->orderBy('starts_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Update appointment status (barber can only confirm/cancel their own appointments)
     */
    public function updateAppointmentStatus(Request $request, Appointment $appointment)
    {
        $user = $request->user();
        
        // Verificar que el usuario sea un barbero
        $barber = Barber::where('user_id', $user->id)->first();
        
        if (!$barber) {
            return response()->json([
                'success' => false,
                'message' => 'No se encontró perfil de barbero'
            ], 403);
        }

        // Verificar que el turno pertenezca al barbero
        if ($appointment->barber_id !== $barber->id) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para modificar este turno'
            ], 403);
        }

        $request->validate([
            'status' => 'required|in:CONFIRMED,CANCELLED'
        ]);

        $appointment->status = $request->status;
        $appointment->save();

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente',
            'data' => $appointment->load(['service', 'barber'])
        ]);
    }
}
