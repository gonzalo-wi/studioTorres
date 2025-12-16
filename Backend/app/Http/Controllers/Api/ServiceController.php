<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * GET /api/services
     */
    public function index()
    {
        $services = Service::active()->get();
        return $this->success($services);
    }

    /**
     * GET /api/services/{id}
     */
    public function show(Service $service)
    {
        if (!$service->active) {
            return $this->error(
                'SERVICE_NOT_FOUND',
                'El servicio no está disponible',
                null,
                404
            );
        }

        return $this->success($service);
    }
}
