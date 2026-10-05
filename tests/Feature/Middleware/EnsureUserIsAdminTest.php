<?php

use App\Models\User;

it('bloqueia visitante sem autenticação com 401', function () {
    $this->getJson('/api/admin/rooms')
        ->assertStatus(401);
});

it('bloqueia user autenticado sem role admin com 403', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->getJson('/api/admin/rooms')
        ->assertStatus(403);
});

it('permite admin aceder com 200', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->getJson('/api/admin/rooms')
        ->assertStatus(200);
});
