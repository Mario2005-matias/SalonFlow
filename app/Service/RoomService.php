<?php

namespace App\Service;

use App\Models\Room;

class RoomService
{
    public function listFiltered(array $filters)
    {
        return Room::query()
            ->with('category')
            ->where('is_available', true)
            ->when($filters['category'] ?? null, function ($query, $slug) {
                $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
            })
            ->when($filters['capacity_min'] ?? null, fn ($query, $val) =>
                $query->where('capacity', '>=', $val)
            )
            ->when($filters['price_min'] ?? null, fn ($query, $val) =>
                $query->where('price', '>=', $val)
            )
            ->when($filters['price_max'] ?? null, fn ($query, $val) =>
                $query->where('price', '<=', $val)
            )
            ->paginate($filters['per_page'] ?? 12);
    }
}
