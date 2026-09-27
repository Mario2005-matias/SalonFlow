<?php

use App\Models\User;
use App\Models\Room;
use function Pest\Laravel\{actingAs, getJson, postJson, putJson};

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lista todas as salas', function () {
    Room::factory()->count(3)->create();

    actingAs($this->user)
        ->getJson('/rooms')
        ->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'data' => [
                '*' => ['id', 'name', 'description', 'capaity', 'location', 'is_available']
            ]
        ]);
});

it('cria uma sala com sucesso', function () {
    $data = [
        'name' => 'Sala de Reunião A',
        'description' => 'Sala grande com projetor',
        'capacity' => 20,
        'location' => 'Andar 2',
        'is_available' => true,
    ];

    actingAs($this->user)
        ->postJson('/room', $data)
        ->assertStatus(201)
        ->assertJsonPath('message', 'Sala criada com sucesso');

    $this->assertDatabaseHas('rooms', [
        'name' => 'Sala de Reunião A',
        'capacity' => 20,
    ]);
});

it('mostra uma sala específica', function () {
    $room = Room::factory()->create();

    actingAs($this->user)
        ->getJson("/rooms/{$room->id}")
        ->assertStatus(200)
        ->assertJsonPath('data.id', $room->id);
});

it('atualiza uma sala', function () {
    $room = Room::factory()->create();

    actingAs($this->user)
        ->putJson("/rooms/{$room->id}", [
            'name' => 'Sala Atualizada',
            'description' => 'Nova descrição',
            'capacity' => 30,
            'location' => 'Andar 3',
        ])
        ->assertStatus(200);

    $this->assertDatabaseHas('rooms', [
        'id' => $room->id,
        'name' => 'Sala Atualizada',
    ]);
});

it('desabilita uma sala', function () {
    $room = Room::factory()->create(['is_available' => true]);

    actingAs($this->user)
        ->putJson("/rooms/{$room->id}/disable", [
            'is_available' => false,
        ])
        ->assertStatus(200)
        ->assertJson(['message' => 'Sala desabilitada com sucesso']);

    expect($room->fresh()->is_available)->toBeFalse();
});

it('não desabilita sala já desabilitada', function () {
    $room = Room::factory()->create(['is_available' => false]);

    actingAs($this->user)
        ->putJson("/rooms/{$room->id}/disable", [
            'is_available' => false,
        ])
        ->assertStatus(409);
});
