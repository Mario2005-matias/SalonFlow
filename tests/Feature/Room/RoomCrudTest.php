<?php

use App\Models\Room;
use App\Service\RoomService;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->service = new RoomService();
});

it('desabilita uma sala disponível', function () {
    $room = Room::factory()->create(['is_available' => true]);

    $this->service->disable($room);

    expect($room->fresh()->is_available)->toBeFalse();
});

it('lança exceção ao desabilitar sala já desabilitada', function () {
    $room = Room::factory()->create(['is_available' => false]);

    $this->service->disable($room);
})->throws(DomainException::class);

it('habilita uma sala indisponível', function () {
    $room = Room::factory()->create(['is_available' => false]);

    $this->service->enable($room);

    expect($room->fresh()->is_available)->toBeTrue();
});

it('lança exceção ao habilitar sala já habilitada', function () {
    $room = Room::factory()->create(['is_available' => true]);

    $this->service->enable($room);
})->throws(DomainException::class);
