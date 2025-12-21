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

    /**
     * GET /api/admin/reports?start_date=&end_date=
     */
    public function reports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        \Log::info('Cargando reportes', ['start_date' => $startDate, 'end_date' => $endDate]);

        // Estadísticas por barbero
        $barberStats = \DB::select("
            SELECT 
                b.id as barber_id,
                b.name as barber_name,
                COUNT(a.id) as total_appointments,
                COALESCE(SUM(CASE WHEN a.status = 'CONFIRMED' THEN 1 ELSE 0 END), 0) as confirmed_appointments,
                COALESCE(SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END), 0) as pending_appointments,
                COALESCE(SUM(CASE WHEN a.status = 'CANCELLED' THEN 1 ELSE 0 END), 0) as cancelled_appointments,
                COALESCE(SUM(CASE WHEN a.status = 'CONFIRMED' THEN s.price ELSE 0 END), 0) as total_revenue
            FROM barbers b
            LEFT JOIN appointments a ON b.id = a.barber_id 
                AND DATE(a.starts_at) BETWEEN ? AND ?
            LEFT JOIN services s ON a.service_id = s.id
            WHERE b.active = 1
            GROUP BY b.id, b.name
            HAVING total_appointments > 0
            ORDER BY total_revenue DESC
        ", [$startDate, $endDate]);

        // Estadísticas por servicio
        $serviceStats = \DB::select("
            SELECT 
                s.id as service_id,
                s.title as service_name,
                COUNT(a.id) as total_appointments,
                COALESCE(SUM(CASE WHEN a.status = 'CONFIRMED' THEN s.price ELSE 0 END), 0) as total_revenue
            FROM services s
            INNER JOIN appointments a ON s.id = a.service_id 
                AND DATE(a.starts_at) BETWEEN ? AND ?
            WHERE s.active = 1
            GROUP BY s.id, s.title, s.price
            HAVING total_appointments > 0
            ORDER BY total_appointments DESC
        ", [$startDate, $endDate]);

        // Resumen general
        $totalAppointments = Appointment::whereDate('starts_at', '>=', $startDate)
            ->whereDate('starts_at', '<=', $endDate)
            ->count();

        $totalRevenue = Appointment::whereDate('starts_at', '>=', $startDate)
            ->whereDate('starts_at', '<=', $endDate)
            ->where('status', 'CONFIRMED')
            ->join('services', 'appointments.service_id', '=', 'services.id')
            ->sum('services.price');

        $confirmedCount = Appointment::whereDate('starts_at', '>=', $startDate)
            ->whereDate('starts_at', '<=', $endDate)
            ->where('status', 'CONFIRMED')
            ->count();

        $summary = [
            'total_appointments' => $totalAppointments,
            'total_revenue' => (float)$totalRevenue,
            'average_ticket' => $confirmedCount > 0 ? (float)$totalRevenue / $confirmedCount : 0
        ];

        \Log::info('Reportes cargados', [
            'barbers' => count($barberStats),
            'services' => count($serviceStats),
            'summary' => $summary
        ]);

        return $this->success([
            'barber_stats' => $barberStats,
            'service_stats' => $serviceStats,
            'summary' => $summary
        ]);
    }

    /**
     * GET /api/admin/reports/export?start_date=&end_date=
     */
    public function exportReports(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));

        // Obtener datos
        $barberStats = \DB::select("
            SELECT 
                b.name as 'Barbero',
                COUNT(a.id) as 'Total Turnos',
                SUM(CASE WHEN a.status = 'CONFIRMED' THEN 1 ELSE 0 END) as 'Confirmados',
                SUM(CASE WHEN a.status = 'PENDING' THEN 1 ELSE 0 END) as 'Pendientes',
                SUM(CASE WHEN a.status = 'CANCELLED' THEN 1 ELSE 0 END) as 'Cancelados',
                SUM(CASE WHEN a.status = 'CONFIRMED' THEN s.price ELSE 0 END) as 'Ingresos Totales'
            FROM barbers b
            LEFT JOIN appointments a ON b.id = a.barber_id 
                AND DATE(a.starts_at) BETWEEN ? AND ?
            LEFT JOIN services s ON a.service_id = s.id
            GROUP BY b.id, b.name
            ORDER BY 'Ingresos Totales' DESC
        ", [$startDate, $endDate]);

        // Crear archivo CSV
        $filename = "reporte_barberia_{$startDate}_{$endDate}.csv";
        $handle = fopen('php://temp', 'r+');
        
        // BOM para UTF-8
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        if (count($barberStats) > 0) {
            fputcsv($handle, array_keys((array)$barberStats[0]), ';');
            
            // Datos
            foreach ($barberStats as $row) {
                fputcsv($handle, (array)$row, ';');
            }
        }
        
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
