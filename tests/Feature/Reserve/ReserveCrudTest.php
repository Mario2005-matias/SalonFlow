<?php

use App\Models\Category;
use App\Models\Room;
use App\Models\User;

it('admin cria uma sala', function () {
    $admin    = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $response = $this->actingAs($admin)->postJson('/api/admin/rooms', [
        'name'         => 'Salão VIP',
        'description'  => 'Salão premium',
        'capacity'     => 20,
        'location'     => 'Piso 2',
        'is_available' => true,
        'category_id'  => $category->id,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Salão VIP');

    $this->assertDatabaseHas('rooms', ['name' => 'Salão VIP']);
});

it('admin não cria sala com dados inválidos', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/api/admin/rooms', [
            'name' => '',
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'description', 'capacity', 'location', 'category_id']);
});

it('admin atualiza uma sala', function () {
    $admin = User::factory()->admin()->create();
    $room  = Room::factory()->create(['name' => 'Antigo']);

    $this->actingAs($admin)
        ->putJson("/api/admin/rooms/{$room->id}", [
            'name'         => 'Novo Nome',
            'description'  => $room->description,
            'capacity'     => $room->capacity,
            'location'     => $room->location,
            'is_available' => true,
            'category_id'  => $room->category_id,
        ])
        ->assertStatus(200);

    expect($room->fresh()->name)->toBe('Novo Nome');
});

it('admin desabilita e habilita uma sala', function () {
    $admin = User::factory()->admin()->create();
    $room  = Room::factory()->create(['is_available' => true]);

    $this->actingAs($admin)
        ->putJson("/api/admin/rooms/{$room->id}/disable")
        ->assertStatus(200);

    expect($room->fresh()->is_available)->toBeFalse();

    $this->actingAs($admin)
        ->putJson("/api/admin/rooms/{$room->id}/enable")
        ->assertStatus(200);

    expect($room->fresh()->is_available)->toBeTrue();
});

it('listagem pública mostra apenas salas disponíveis', function () {
    Room::factory()->count(3)->create(['is_available' => true]);
    Room::factory()->count(2)->create(['is_available' => false]);

    $response = $this->getJson('/api/rooms');

    $response->assertStatus(200);
    expect($response->json('data'))->toHaveCount(3);
});
