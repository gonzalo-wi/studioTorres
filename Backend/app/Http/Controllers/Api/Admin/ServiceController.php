<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;

class ServiceController extends Controller
{
    /**
     * GET /api/admin/services
     */
    public function index()
    {
        $services = Service::orderBy('created_at', 'desc')->get();
        return $this->success($services);
    }

    /**
     * POST /api/admin/services
     */
    public function store(StoreServiceRequest $request)
    {
        $service = Service::create($request->validated());

        return $this->success([
            'service' => $service,
            'message' => 'Servicio creado exitosamente',
        ], 201);
    }

    /**
     * PUT /api/admin/services/{id}
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        $service->update($request->validated());

        return $this->success([
            'service' => $service->fresh(),
            'message' => 'Servicio actualizado exitosamente',
        ]);
    }

    /**
     * DELETE /api/admin/services/{id}
     */
    public function destroy(Service $service)
    {
        // Check if service has appointments
        if ($service->appointments()->exists()) {
            return $this->error(
                'SERVICE_HAS_APPOINTMENTS',
                'No se puede eliminar un servicio con turnos asociados',
                'Considera desactivarlo en lugar de eliminarlo',
                422
            );
        }

        $service->delete();

        return $this->success(['message' => 'Servicio eliminado exitosamente']);
    }
}
