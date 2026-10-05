<?php

use App\Models\Reserve;
use App\Models\User;

it('user A não vê reserva de user B', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $reserveB = Reserve::factory()->for($userB)->create();

    $this->actingAs($userA)
        ->getJson("/api/reserves/{$reserveB->id}")
        ->assertStatus(403);
});

it('user A não cancela reserva de user B', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $reserveB = Reserve::factory()->for($userB)->create([
        'start_time' => now()->addDay(),
        'end_time'   => now()->addDay()->addHour(),
    ]);

    $this->actingAs($userA)
        ->putJson("/api/reserves/{$reserveB->id}/cancelation", [
            'status' => 'cancelated',
        ])
        ->assertStatus(403);

    expect($reserveB->fresh()->status)->toBe('approved');
});

it('user vê a própria reserva', function () {
    $user    = User::factory()->create();
    $reserve = Reserve::factory()->for($user)->create();

    $this->actingAs($user)
        ->getJson("/api/reserves/{$reserve->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $reserve->id);
});

it('user cancela a própria reserva', function () {
    $user = User::factory()->create();
    $reserve = Reserve::factory()->for($user)->create([
        'start_time' => now()->addDay(),
        'end_time'   => now()->addDay()->addHour(),
    ]);

    $this->actingAs($user)
        ->putJson("/api/reserves/{$reserve->id}/cancelation", [
            'status' => 'cancelated',
        ])
        ->assertStatus(200);

    expect($reserve->fresh()->status)->toBe('cancelated');
});

it('admin vê reserva de qualquer user', function () {
    $admin   = User::factory()->admin()->create();
    $reserve = Reserve::factory()->create();

    $this->actingAs($admin)
        ->getJson("/api/admin/reserves/{$reserve->id}")
        ->assertStatus(200);
});

it('admin cancela reserva de qualquer user', function () {
    $admin   = User::factory()->admin()->create();
    $reserve = Reserve::factory()->create([
        'start_time' => now()->addDay(),
        'end_time'   => now()->addDay()->addHour(),
    ]);

    $this->actingAs($admin)
        ->putJson("/api/reserves/{$reserve->id}/cancelation", [
            'status' => 'cancelated',
        ])
        ->assertStatus(200);
});

it('index devolve só reservas do user autenticado', function () {
    $user = User::factory()->create();
    Reserve::factory()->for($user)->count(3)->create();
    Reserve::factory()->count(5)->create(); // de outros users

    $response = $this->actingAs($user)->getJson('/api/reserves');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(3);
});
