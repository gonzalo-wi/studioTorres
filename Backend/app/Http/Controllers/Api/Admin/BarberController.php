<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barber;
use App\Models\BarberSchedule;
use App\Models\Appointment;
use Illuminate\Http\Request;

class BarberController extends Controller
{
    public function index()
    {
        $barbers = Barber::withCount(['appointments' => function($q){
            $q->whereDate('starts_at', now()->toDateString());
        }])->orderBy('name')->get();

        return $this->success($barbers);
    }

    public function show(Barber $barber)
    {
        $barber->load('schedules');
        return $this->success($barber);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'active' => 'boolean',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'avatar_url' => 'nullable|string',
            'earnings_type' => 'in:FIJO,PORCENTAJE',
            'earnings_value' => 'numeric|min:0',
        ]);

        $barber = Barber::create($data);
        return $this->success(['barber' => $barber], 201);
    }

    public function update(Request $request, Barber $barber)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:100',
            'active' => 'sometimes|boolean',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email',
            'avatar_url' => 'nullable|string',
            'earnings_type' => 'in:FIJO,PORCENTAJE',
            'earnings_value' => 'numeric|min:0',
        ]);

        $barber->update($data);
        return $this->success(['barber' => $barber->fresh('schedules')]);
    }

    public function destroy(Barber $barber)
    {
        if ($barber->appointments()->exists()) {
            return $this->error('BARBER_HAS_APPOINTMENTS', 'No se puede eliminar: tiene turnos asociados', null, 422);
        }
        $barber->delete();
        return $this->success(['message' => 'Barbero eliminado']);
    }

    public function updateSchedules(Request $request, Barber $barber)
    {
        $items = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.weekday' => 'required|integer|min:0|max:6',
            'schedules.*.start_time' => 'required|date_format:H:i',
            'schedules.*.end_time' => 'required|date_format:H:i|after:schedules.*.start_time',
            'schedules.*.break_start' => 'nullable|date_format:H:i',
            'schedules.*.break_end' => 'nullable|date_format:H:i',
        ]);

        foreach ($items['schedules'] as $sch) {
            BarberSchedule::updateOrCreate(
                ['barber_id' => $barber->id, 'weekday' => $sch['weekday']],
                [
                    'start_time' => $sch['start_time'],
                    'end_time' => $sch['end_time'],
                    'break_start' => $sch['break_start'] ?? null,
                    'break_end' => $sch['break_end'] ?? null,
                ]
            );
        }

        return $this->success(['barber' => $barber->fresh('schedules')]);
    }

    public function appointments(Request $request, Barber $barber)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $status = $request->query('status');

        $q = Appointment::with('service')
            ->where('barber_id', $barber->id)
            ->orderBy('starts_at', 'desc');

        if ($from) { $q->whereDate('starts_at', '>=', $from); }
        if ($to) { $q->whereDate('starts_at', '<=', $to); }
        if ($status) { $q->where('status', $status); }

        return $this->success($q->paginate(20));
    }

    public function earnings(Request $request, Barber $barber)
    {
        $from = $request->query('from', now()->startOfMonth()->toDateString());
        $to = $request->query('to', now()->endOfMonth()->toDateString());

        $appointments = Appointment::with('service')
            ->where('barber_id', $barber->id)
            ->whereDate('starts_at', '>=', $from)
            ->whereDate('starts_at', '<=', $to)
            ->whereIn('status', ['CONFIRMED','DONE'])
            ->get();

        $total = 0.0;
        foreach ($appointments as $a) {
            $price = $a->service->price ?? 0.0;
            if ($barber->earnings_type === 'FIJO') {
                $total += (float)$barber->earnings_value;
            } else {
                $total += $price * ((float)$barber->earnings_value / 100.0);
            }
        }

        return $this->success([
            'from' => $from,
            'to' => $to,
            'appointments' => $appointments->count(),
            'total' => round($total, 2),
        ]);
    }
}
