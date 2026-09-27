<?php

namespace App\Http\Concerns;


namespace App\Http\Controllers\Concerns;

trait ApiResponses
{
    protected function success(string $message, mixed $data = null, int $status = 200)
    {
        return response()->json(array_filter([
            'message' => $message,
            'data' => $data,
        ], fn($v) => $v !== null), $status);
    }

    protected function error(string $message, int $status)
    {
        return response()->json(['message' => $message], $status);
    }
}
