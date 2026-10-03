<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReserveResource;
use App\Models\Reserve;
use App\Http\Requests\ReserveStore;
use App\Http\Requests\ReserveCancelation;
use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Requests\FilterReservesRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReserveController extends Controller
{
    use ApiResponses;

    public function index(Request $request)
    {
        $reservations = Reserve::with('room')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('start_time')
            ->paginate(20);

        return $this->success(
            'Reservas encontradas',
            ReserveResource::collection($reservations)
        );
    }

    public function indexAdmin(FilterReservesRequest $request)
    {
        $filters = $request->validated();

        $reservations = Reserve::query()
            ->with(['room', 'user'])
            ->when(
                $filters['status'] ?? null,
                fn($q, $status) => $q->where('status', $status)
            )
            ->when(
                $filters['room_id'] ?? null,
                fn($q, $roomId) => $q->where('room_id', $roomId)
            )
            ->when(
                $filters['user_id'] ?? null,
                fn($q, $userId) => $q->where('user_id', $userId)
            )
            ->when(
                $filters['date_from'] ?? null,
                fn($q, $from) => $q->where('end_time', '>=', $from)
            )
            ->when(
                $filters['date_to'] ?? null,
                fn($q, $to) => $q->where('start_time', '<=', $to)
            )
            ->orderByDesc('start_time')
            ->paginate($filters['per_page'] ?? 20);

        return $this->success(
            'Reservas encontradas',
            ReserveResource::collection($reservations)
        );
    }


    public function store(ReserveStore $request)
    {
        dd($request->validated());
        $data = $request->validated();

        $reservation = DB::transaction(function () use ($request, $data) {
            $conflict = Reserve::where('room_id', $data['room_id'])
                ->where('status', 'approved')
                ->where('start_time', '<', $data['end_time'])
                ->where('end_time', '>', $data['start_time'])
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                abort(409, 'A sala já está reservada nesse período.');
            }

            return Reserve::create([
                ...$data,
                'user_id' => $request->user()->id,
            ]);
        });

        return $this->success(
            'Reserva criada com sucesso',
            new ReserveResource($reservation),
            201
        );
    }

    public function show(Reserve $reserve)
    {
        $this->authorize('view', $reserve);

        return $this->success(
            'Reserva encontrada',
            new ReserveResource($reserve->load('room', 'user'))
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function cancelation(ReserveCancelation $request, Reserve $reserve)
    {
        $this->authorize('update', $reserve);

        if ($reserve->status === 'cancelated') {
            return $this->error('Esta reserva já está cancelada.', 409);
        }

        if ($reserve->end_time < now()) {
            return $this->error('Não é possível cancelar uma reserva que já passou.', 400);
        }

        $reserve->update($request->validated());

        return $this->success(
            'Reserva cancelada com sucesso',
            new ReserveResource($reserve->fresh())
        );
    }

    public function destroy(string $id)
    {
        //
    }
}
