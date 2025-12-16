<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
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
     * POST /api/appointments
     */
    public function store(StoreAppointmentRequest $request)
    {
        try {
            $appointment = $this->appointmentService->createAppointment($request->validated());

            return $this->success([
                'appointment' => $appointment,
                'message' => '¡Turno reservado exitosamente!',
            ], 201);
        } catch (\Exception $e) {
            return $this->error(
                'BOOKING_FAILED',
                $e->getMessage(),
                null,
                422
            );
        }
    }

    /**
     * GET /api/appointments/{public_code}
     */
    public function show(string $publicCode)
    {
        $appointment = $this->appointmentService->getAppointmentByPublicCode($publicCode);

        if (!$appointment) {
            return $this->error(
                'APPOINTMENT_NOT_FOUND',
                'No se encontró el turno con ese código',
                null,
                404
            );
        }

        return $this->success($appointment);
    }
}
