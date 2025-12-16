<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    private AvailabilityService $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->availabilityService = $availabilityService;
    }

    /**
     * GET /api/availability?date=YYYY-MM-DD&service_id=1
     */
    public function index(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'service_id' => 'nullable|exists:services,id',
        ]);

        $date = $request->input('date');
        $serviceId = $request->input('service_id');

        try {
            $slots = $this->availabilityService->getAvailableSlots($date, $serviceId);

            return $this->success([
                'date' => $date,
                'slots' => $slots,
            ]);
        } catch (\Exception $e) {
            return $this->error(
                'AVAILABILITY_ERROR',
                'Error al obtener disponibilidad',
                $e->getMessage(),
                500
            );
        }
    }
}
