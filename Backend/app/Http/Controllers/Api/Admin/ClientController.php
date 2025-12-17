<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * GET /api/admin/clients
     * List all clients with their appointment history
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'CLIENT');

        // Search by name, email, or phone
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('created_at', 'desc')
            ->paginate(20)
            ->through(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                    'created_at' => $client->created_at,
                ];
            });

        return $this->success($clients);
    }

    /**
     * GET /api/admin/clients/{id}
     * Show client details with appointment history
     */
    public function show(User $client)
    {
        if ($client->role !== 'CLIENT') {
            return $this->error('NOT_CLIENT', 'El usuario no es un cliente', null, 404);
        }

        // Get appointments by phone/email match (since clients might not have accounts)
        $appointments = Appointment::with(['service', 'barber.user'])
            ->where(function ($q) use ($client) {
                $q->where('client_email', $client->email)
                    ->orWhere('client_phone', $client->phone)
                    ->orWhere('client_name', 'like', "%{$client->name}%");
            })
            ->orderBy('starts_at', 'desc')
            ->get()
            ->map(function ($appointment) {
                return [
                    'id' => $appointment->id,
                    'public_code' => $appointment->public_code,
                    'service' => $appointment->service->title,
                    'barber' => $appointment->barber->user->name,
                    'starts_at' => $appointment->starts_at,
                    'ends_at' => $appointment->ends_at,
                    'status' => $appointment->status,
                    'notes' => $appointment->notes,
                ];
            });

        $stats = [
            'total_appointments' => $appointments->count(),
            'completed' => $appointments->where('status', 'DONE')->count(),
            'cancelled' => $appointments->where('status', 'CANCELLED')->count(),
            'no_show' => $appointments->where('status', 'NO_SHOW')->count(),
        ];

        return $this->success([
            'client' => [
                'id' => $client->id,
                'name' => $client->name,
                'email' => $client->email,
                'phone' => $client->phone,
                'created_at' => $client->created_at,
            ],
            'appointments' => $appointments,
            'stats' => $stats,
        ]);
    }

    /**
     * GET /api/admin/clients/search
     * Search for clients by appointment data (for those without accounts)
     */
    public function searchByAppointments(Request $request)
    {
        $validated = $request->validate([
            'search' => 'required|string|min:3',
        ]);

        // Search in appointments
        $appointments = Appointment::with(['service', 'barber.user'])
            ->where(function ($q) use ($validated) {
                $search = $validated['search'];
                $q->where('client_name', 'like', "%{$search}%")
                    ->orWhere('client_phone', 'like', "%{$search}%")
                    ->orWhere('client_email', 'like', "%{$search}%");
            })
            ->orderBy('starts_at', 'desc')
            ->limit(50)
            ->get();

        // Group by client (using phone as identifier)
        $clients = $appointments->groupBy('client_phone')->map(function ($clientAppointments) {
            $latest = $clientAppointments->first();
            
            return [
                'client_name' => $latest->client_name,
                'client_phone' => $latest->client_phone,
                'client_email' => $latest->client_email,
                'appointments_count' => $clientAppointments->count(),
                'last_appointment' => $latest->starts_at,
                'appointments' => $clientAppointments->map(function ($apt) {
                    return [
                        'id' => $apt->id,
                        'public_code' => $apt->public_code,
                        'service' => $apt->service->title,
                        'barber' => $apt->barber->user->name,
                        'starts_at' => $apt->starts_at,
                        'status' => $apt->status,
                    ];
                }),
            ];
        })->values();

        return $this->success($clients);
    }
}
