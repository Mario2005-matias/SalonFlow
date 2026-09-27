<?php

namespace App\Service;

use App\Models\Room;
use Illuminate\Support\Facades\DB;

class RoomService
{
    public function listAll()
    {
        return Room::with('category')->paginate(15);
    }

    public function listFiltered(array $filters)
    {
        return Room::query()
            ->with('category')
            ->where('is_available', true)
            ->when(
                $filters['category'] ?? null,
                fn($q, $slug) =>
                $q->whereHas('category', fn($q) => $q->where('slug', $slug))
            )
            ->when(
                $filters['capacity_min'] ?? null,
                fn($q, $val) =>
                $q->where('capacity', '>=', $val)
            )
            ->when(
                $filters['price_min'] ?? null,
                fn($q, $val) =>
                $q->where('price', '>=', $val)
            )
            ->when(
                $filters['price_max'] ?? null,
                fn($q, $val) =>
                $q->where('price', '<=', $val)
            )
            ->paginate($filters['per_page'] ?? 12);
    }

    public function create(array $data): Room
    {
        return DB::transaction(fn() => Room::create($data));
    }

    public function update(Room $room, array $data): Room
    {
        $room->update($data);
        return $room->fresh('category');
    }

    public function disable(Room $room): Room
    {
        if (!$room->is_available) {
            throw new \DomainException('Esta sala já está desabilitada');
        }

        $room->update(['is_available' => false]);
        return $room;
    }

    public function enable(Room $room): Room
    {
        if ($room->is_available) {
            throw new \DomainException('Esta sala já está habilitada');
        }

        $room->update(['is_available' => true]);
        return $room;
    }
}
