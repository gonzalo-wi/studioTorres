<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barber;

class BarberController extends Controller
{
    /**
     * GET /api/barbers - Get all active barbers
     */
    public function index()
    {
        $barbers = Barber::where('active', true)->get();
        return $this->success($barbers);
    }

    /**
     * GET /api/barbers/{id} - Get single barber
     */
    public function show(Barber $barber)
    {
        if (!$barber->active) {
            return $this->error(
                'BARBER_NOT_FOUND',
                'El barbero no está disponible',
                null,
                404
            );
        }

        return $this->success($barber);
    }
}
