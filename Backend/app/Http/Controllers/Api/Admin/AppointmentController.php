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
        $today = now()->format('Y-m-d');
        
        $stats = [
            'today_appointments' => Appointment::where('date', $today)->count(),
            'pending' => Appointment::where('status', 'PENDING')->count(),
            'confirmed' => Appointment::where('status', 'CONFIRMED')->count(),
            'cancelled_today' => Appointment::where('date', $today)
                ->where('status', 'CANCELLED')
                ->count(),
        ];

        return $this->success($stats);
    }
}
