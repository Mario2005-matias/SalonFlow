<?php

use App\Models\Category;
use App\Models\User;

it('admin lista categorias', function () {
    Category::factory()->count(3)->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->getJson('/api/admin/categories')
        ->assertStatus(200);
});

it('admin cria categoria', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->postJson('/api/admin/categories', ['name' => 'Salão de Festas'])
        ->assertStatus(201)
        ->assertJsonPath('data.slug', 'salao-de-festas');

    $this->assertDatabaseHas('categories', ['slug' => 'salao-de-festas']);
});

it('admin atualiza categoria', function () {
    $admin    = User::factory()->admin()->create();
    $category = Category::factory()->create(['name' => 'Antigo']);

    $this->actingAs($admin)
        ->putJson("/api/admin/categories/{$category->id}", ['name' => 'Novo'])
        ->assertStatus(200);

    expect($category->fresh()->name)->toBe('Novo');
    expect($category->fresh()->slug)->toBe('novo');
});

it('admin elimina categoria', function () {
    $admin    = User::factory()->admin()->create();
    $category = Category::factory()->create();

    $this->actingAs($admin)
        ->deleteJson("/api/admin/categories/{$category->id}")
        ->assertStatus(200);

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});
