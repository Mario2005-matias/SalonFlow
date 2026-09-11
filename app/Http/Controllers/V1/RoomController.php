<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomDisableRequest;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::all();
        return response()->json([
            'message' => 'Salas encontradas',
            'data' => RoomResource::collection($rooms)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoomRequest $request)
    {
        $room = Room::create($request->validated());

        return response()->json([
            'message' => 'Sala criada com sucesso',
            'data' => new RoomResource($room)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Room $room)
    {
        return response()->json([
            'message' => 'Sala encontrada',
            'data' => new RoomResource($room)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoomRequest $request, Room $room)
    {
        $room->update($request->validated());
        return response()->json([
            'message' => 'Sala atualizada com sucesso',
            'data' => new RoomResource($room)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function disable(RoomDisableRequest $request, Room $room)
    {
        if(!$room->is_available) {
            return response()->json(['message' => 'Esta sala já está desabilitada'], 409);
        }

        $room->update($request->validated());

        return response()->json(['message' => 'Sala desabilitada com sucesso']);
    }

    public function enable(RoomDisableRequest $request, Room $room)
    {
        if($room->is_available) {
            return response()->json(['message' => 'Esta sala já está habilitada'], 409);
        }

        $room->update($request->validated());

        return response()->json(['message' => 'Sala habilitada com sucesso'], 200);
    }
}
