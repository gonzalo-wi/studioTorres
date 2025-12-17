<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    private AppointmentService $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * GET /api/admin/appointments?status=&from=&to=&page=
     */
    public function index(Request $request)
    {
        $filters = $request->only(['status', 'from', 'to']);
        $appointments = $this->appointmentService->getFilteredAppointments($filters);

        return $this->success($appointments);
    }

    /**
     * GET /api/admin/appointments/{id}
     */
    public function show(Appointment $appointment)
    {
        $appointment->load(['service', 'barber']);
        return $this->success($appointment);
    }

    /**
     * PUT /api/admin/appointments/{id}/status
     */
    public function updateStatus(UpdateAppointmentStatusRequest $request, Appointment $appointment)
    {
        try {
            $updated = $this->appointmentService->updateAppointmentStatus(
                $appointment,
                $request->validated()
            );

            return $this->success([
                'appointment' => $updated,
                'message' => 'Estado actualizado correctamente',
            ]);
        } catch (\Exception $e) {
            return $this->error(
                'UPDATE_FAILED',
                $e->getMessage(),
                null,
                422
            );
        }
    }

    /**
     * GET /api/admin/dashboard/stats
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $endOfDay = now()->endOfDay();
        
        $stats = [
            'today_appointments' => Appointment::whereBetween('starts_at', [$today, $endOfDay])->count(),
            'pending' => Appointment::where('status', 'PENDING')->count(),
            'confirmed' => Appointment::where('status', 'CONFIRMED')->count(),
            'cancelled_today' => Appointment::whereBetween('starts_at', [$today, $endOfDay])
                ->where('status', 'CANCELLED')
                ->count(),
        ];

        return $this->success($stats);
    }

    /**
     * GET /api/admin/dashboard/monthly-stats
     */
    public function monthlyStats()
    {
        $currentMonth = now()->startOfMonth();
        $lastSixMonths = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $appointments = Appointment::whereBetween('starts_at', [$month, $monthEnd])->get();
            
            $lastSixMonths[] = [
                'month' => $month->format('Y-m'),
                'label' => $month->locale('es')->translatedFormat('F Y'),
                'total' => $appointments->count(),
                'confirmed' => $appointments->where('status', 'CONFIRMED')->count(),
                'pending' => $appointments->where('status', 'PENDING')->count(),
                'cancelled' => $appointments->where('status', 'CANCELLED')->count(),
                'revenue' => $appointments->where('status', 'CONFIRMED')
                    ->sum(function($apt) {
                        return $apt->service->price ?? 0;
                    })
            ];
        }
        
        return $this->success($lastSixMonths);
    }
}
