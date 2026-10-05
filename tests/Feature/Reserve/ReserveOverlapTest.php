<?php

use App\Models\Reserve;
use App\Models\Room;
use App\Models\User;

beforeEach(function () {
    $this->travelTo(\Carbon\Carbon::parse('2026-01-01 08:00:00'));

    $this->user = User::factory()->create();
    $this->room = Room::factory()->create(['is_available' => true]);

    Reserve::factory()
        ->for($this->user)
        ->for($this->room)
        ->between('2026-06-01 10:00:00', '2026-06-01 12:00:00')
        ->create();
});

dataset('conflitos', [
    'sobrepõe início'          => ['2026-06-01 11:00:00', '2026-06-01 13:00:00'],
    'sobrepõe fim'             => ['2026-06-01 09:00:00', '2026-06-01 11:00:00'],
    'contido dentro'           => ['2026-06-01 10:30:00', '2026-06-01 11:30:00'],
    'engloba por completo'     => ['2026-06-01 09:00:00', '2026-06-01 13:00:00'],
    'igual à existente'        => ['2026-06-01 10:00:00', '2026-06-01 12:00:00'],
]);

dataset('sem conflito', [
    'encosta no fim'    => ['2026-06-01 12:00:00', '2026-06-01 13:00:00'],
    'encosta no início' => ['2026-06-01 08:00:00', '2026-06-01 10:00:00'],
    'totalmente antes'  => ['2026-06-01 06:00:00', '2026-06-01 08:00:00'],
    'totalmente depois' => ['2026-06-01 14:00:00', '2026-06-01 16:00:00'],
]);

it('rejeita reserva em conflito', function (string $start, string $end) {
    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $this->room->id,
            'start_time' => $start,
            'end_time'   => $end,
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(409);
})->with('conflitos');

it('aceita reserva sem conflito', function (string $start, string $end) {
    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $this->room->id,
            'start_time' => $start,
            'end_time'   => $end,
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(201);
})->with('sem conflito');

it('ignora reservas canceladas na verificação de conflito', function () {
    Reserve::query()->update(['status' => 'cancelated']);

    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $this->room->id,
            'start_time' => '2026-06-01 10:00:00',
            'end_time'   => '2026-06-01 12:00:00',
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(201);
});

it('não considera conflito de reservas noutra sala', function () {
    $outraSala = Room::factory()->create(['is_available' => true]);

    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $outraSala->id,
            'start_time' => '2026-06-01 10:00:00',
            'end_time'   => '2026-06-01 12:00:00',
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(201);
});

it('rejeita reserva com start_time no passado', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $this->room->id,
            'start_time' => now()->subDay()->toDateTimeString(),
            'end_time'   => now()->subDay()->addHour()->toDateTimeString(),
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(422);
});

it('rejeita reserva com end_time antes do start_time', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/reserves', [
            'room_id'    => $this->room->id,
            'start_time' => '2026-06-01 12:00:00',
            'end_time'   => '2026-06-01 10:00:00',
            'reason'     => 'Teste',
        ]);

    $response->assertStatus(422);
});
