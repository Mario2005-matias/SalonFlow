<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Requests\FilterRoomsRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Service\RoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    use ApiResponses;

    public function __construct(private RoomService $roomService) {}

    public function index()
    {
        $this->authorize('viewAny', Room::class);

        return $this->success(
            'Salas encontradas',
            RoomResource::collection($this->roomService->listAll())
        );
    }

    public function indexClient(FilterRoomsRequest $request)
    {
        return $this->success(
            'Salas encontradas',
            RoomResource::collection($this->roomService->listFiltered($request->validated()))
        );
    }

    public function store(RoomRequest $request)
    {
        $room = $this->roomService->create($request->validated());

        return $this->success('Sala criada com sucesso', new RoomResource($room), 201);
    }

    public function show(Room $room)
    {
        $room->load('category');

        return $this->success('Sala encontrada', new RoomResource($room));
    }

    public function update(RoomRequest $request, Room $room)
    {
        $room = $this->roomService->update($room, $request->validated());

        return $this->success('Sala atualizada com sucesso', new RoomResource($room));
    }

    public function disable(Room $room)
    {
        $this->authorize('update', $room);
        $this->roomService->disable($room);

        return $this->success('Sala desabilitada com sucesso');
    }

    public function enable(Room $room)
    {
        $this->authorize('update', $room);
        $this->roomService->enable($room);

        return $this->success('Sala habilitada com sucesso');
    }
}
