<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\FilterRoomsRequest;
use App\Http\Requests\RoomDisableRequest;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Service\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $rooms = Room::with('category_id')->get();
        return response()->json([
            'message' => 'Salas encontradas',
            'data' => RoomResource::collection($rooms)
        ]);
    }

    public function indexClient(FilterRoomsRequest $request, RoomService $roomService)
    {
        $rooms = $roomService->listFiltered($request->validated());
        return RoomResource::collection($rooms);
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
    public function disable(Room $room): Room
    {
        $room->update(['is_available' => false]);
        return $room;
    }

    public function enable(Room $room): Room
    {
        $room->update(['is_available' => true]);
        return $room;
    }
}
