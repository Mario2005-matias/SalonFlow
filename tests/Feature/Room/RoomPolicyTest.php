<?php

use App\Models\Room;
use App\Models\User;

it('admin pode criar salas', function () {
    $admin = User::factory()->admin()->create();
    expect($admin->can('create', Room::class))->toBeTrue();
});

it('client não pode criar salas', function () {
    $client = User::factory()->create();
    expect($client->can('create', Room::class))->toBeFalse();
});

it('admin pode atualizar qualquer sala', function () {
    $admin = User::factory()->admin()->create();
    $room  = Room::factory()->create();
    expect($admin->can('update', $room))->toBeTrue();
});
