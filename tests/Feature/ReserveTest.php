<?php

use App\Models\User;
use App\Models\Room;
use App\Models\Reserve;
use function Pest\Laravel\{actingAs, getJson, postJson, putJson};

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->room = Room::factory()->create(['is_available' => true]);
});

it('lista as reservas do usuário logado', function () {
    Reserve::factory()->count(2)->create(['user_id' => $this->user->id]);
    Reserve::factory()->create(); // reserva de outro usuário

    actingAs($this->user)
        ->getJson('/reserves')
        ->assertStatus(200)
        ->assertJsonCount(2, 'data');
});

it('cria uma reserva com sucesso', function () {
    $data = [
        'room_id' => $this->room->id,
        'start_time' => now()->addDay()->format('Y-m-d H:i:s'),
        'end_time' => now()->addDay()->addHours(2)->format('Y-m-d H:i:s'),
        'reason' => 'Reunião de equipe',
        'status' => 'pending',
    ];

    actingAs($this->user)
        ->postJson('/reserves', $data)
        ->assertStatus(201)
        ->assertJsonPath('message', 'Reserva criada com sucesso');

    $this->assertDatabaseHas('reserves', [
        'user_id' => $this->user->id,
        'room_id' => $this->room->id,
        'status' => 'pending',
    ]);
});

it('não cria reserva em horário já ocupado', function () {
    $start = now()->addDay();
    $end = $start->copy()->addHours(2);

    // Reserva já existente aprovada
    Reserve::factory()->create([
        'room_id' => $this->room->id,
        'start_time' => $start,
        'end_time' => $end,
        'status' => 'approved',
    ]);

    actingAs($this->user)
        ->postJson('/reserves', [
            'room_id' => $this->room->id,
            'start_time' => $start->addHour()->format('Y-m-d H:i:s'),
            'end_time' => $end->addHour()->format('Y-m-d H:i:s'),
            'status' => 'pending',
        ])
        ->assertStatus(400)
        ->assertJson(['message' => 'A sala já está reservada nesse período']);
});

it('mostra uma reserva do próprio usuário', function () {
    $reserve = Reserve::factory()->create(['user_id' => $this->user->id]);

    actingAs($this->user)
        ->getJson("/reserves/{$reserve->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $reserve->id);
});

it('não mostra reserva de outro usuário', function () {
    $reserve = Reserve::factory()->create(); // outro usuário

    actingAs($this->user)
        ->getJson("/reserves/{$reserve->id}")
        ->assertStatus(403); // ou 404, dependendo de como a policy está
});
