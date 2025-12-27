<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use App\Services\WaitlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class WaitlistController extends Controller
{
    protected $waitlistService;

    public function __construct(WaitlistService $waitlistService)
    {
        $this->waitlistService = $waitlistService;
    }

    /**
     * Agregar cliente a lista de espera
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:255',
            'client_phone' => 'required|string|max:20',
            'client_email' => 'nullable|email|max:255',
            'service_id' => 'required|exists:services,id',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time_start' => 'nullable|date_format:H:i',
            'preferred_time_end' => 'nullable|date_format:H:i|after:preferred_time_start',
            'barber_id' => 'nullable|exists:barbers,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $waitlistEntry = $this->waitlistService->addToWaitlist($request->all());

            return response()->json([
                'message' => '¡Te agregamos a la lista de espera! Te notificaremos cuando haya un turno disponible.',
                'data' => $waitlistEntry->load(['service', 'barber']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al agregar a lista de espera',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener entradas de lista de espera (Admin)
     */
    public function index(Request $request)
    {
        $query = Waitlist::with(['service', 'barber']);

        // Filtros
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('date')) {
            $query->forDate($request->date);
        }

        if ($request->has('service_id')) {
            $query->forService($request->service_id);
        }

        $waitlist = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($waitlist);
    }

    /**
     * Ver detalle de entrada de waitlist
     */
    public function show(string $id)
    {
        $waitlistEntry = Waitlist::with(['service', 'barber'])->findOrFail($id);

        return response()->json([
            'data' => $waitlistEntry,
        ]);
    }

    /**
     * Eliminar entrada de lista de espera (cancelar)
     */
    public function destroy(string $id)
    {
        $waitlistEntry = Waitlist::findOrFail($id);

        // Solo se puede eliminar si está en WAITING o NOTIFIED
        if (!in_array($waitlistEntry->status, ['WAITING', 'NOTIFIED'])) {
            return response()->json([
                'message' => 'No se puede eliminar esta entrada',
            ], 400);
        }

        $waitlistEntry->delete();

        return response()->json([
            'message' => 'Eliminado de lista de espera correctamente',
        ]);
    }

    /**
     * Confirmar turno desde notificación de waitlist
     */
    public function confirmFromWaitlist(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'barber_id' => 'required|exists:barbers,id',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Datos inválidos',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $waitlistEntry = Waitlist::findOrFail($id);

            // Verificar que esté en estado NOTIFIED
            if ($waitlistEntry->status !== 'NOTIFIED') {
                return response()->json([
                    'message' => 'Esta entrada de lista de espera no está disponible',
                ], 400);
            }

            // Verificar que no haya expirado (2 horas)
            if ($waitlistEntry->notified_at->diffInHours(now()) > 2) {
                return response()->json([
                    'message' => 'El tiempo para confirmar este turno ha expirado',
                ], 400);
            }

            // Convertir a turno
            $appointment = $this->waitlistService->convertToAppointment(
                $waitlistEntry,
                $request->only(['barber_id', 'starts_at', 'ends_at'])
            );

            return response()->json([
                'message' => '¡Turno confirmado exitosamente!',
                'data' => $appointment->load(['service', 'barber']),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al confirmar turno',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de lista de espera (Admin)
     */
    public function stats()
    {
        $stats = $this->waitlistService->getStats();

        return response()->json([
            'data' => $stats,
        ]);
    }
}
