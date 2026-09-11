<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReserveResource;
use App\Models\Reserve;
use App\Http\Requests\ReserveStore;
use App\Http\Requests\ReserveCancelation;
use Illuminate\Http\Request;

class ReserveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $reservations = Reserve::where('user_id', $request->user()->id)->get();
        return response()->json([
            'message' => 'Reservas encontradas',
            'data' => ReserveResource::collection($reservations)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ReserveStore $request)
    {
        if(Reserve::where('room_id', $request->room_id)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($query) use ($request) {
                        $query->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })->exists()) {
            return response()->json([
                'message' => 'A sala já está reservada nesse período',
            ], 400);
        }

        $reservation = Reserve::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Reserva criada com sucesso',
            'data' => new ReserveResource($reservation)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reserve $reserve)
    {
        return response()->json([
            'message' => 'Reserva encontrada',
            'data' => new ReserveResource($reserve)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function cancelation(ReserveCancelation $request, Reserve $reserve)
    {
        if($reserve->status === 'cancelated') {
            return response()->json(['message' => 'Esta reserva já está cancelada'], 409);
        }

        if($reserve->end_time >= now()) {
            return response()->json(['message' => 'Não é possível cancelar uma reserva que já passou'], 400);
        }

        $reserve->update($request->validated());

        return response()->json([
            'message' => 'Reserva atualizada com sucesso',
            'data' => new ReserveResource($reserve)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
