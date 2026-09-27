<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use function Pest\Laravel\{postJson, getJson, actingAs};

it('registra um usuário com sucesso', function () {
    $response = postJson('/register', [
        'name' => 'João Silva',
        'email' => 'joao@example.com',
        'password' => 'Senha@123',
        'password_confirmation' => 'Senha@123',
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'access_token',
            'token_type',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'joao@example.com',
    ]);
});

it('não registra com email duplicado', function () {
    User::factory()->create(['email' => 'joao@example.com']);

    postJson('/register', [
        'name' => 'João',
        'email' => 'joao@example.com',
        'password' => 'Senha@123',
        'password_confirmation' => 'Senha@123',
    ])->assertStatus(422);
});

it('faz login com sucesso', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Senha@123'),
    ]);

    $response = postJson('/login', [
        'email' => $user->email,
        'password' => 'Senha@123',
    ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'user',
            'access_token',
            'token_type',
        ]);
});

it('não faz login com senha errada', function () {
    $user = User::factory()->create([
        'password' => Hash::make('Senha@123'),
    ]);

    postJson('/login', [
        'email' => $user->email,
        'password' => 'senha-errada',
    ])->assertStatus(401);
});

it('retorna o perfil do usuário autenticado', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->getJson('/perfil')
        ->assertStatus(200)
        ->assertJson([
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
            ]
        ]);
});

it('faz logout com sucesso', function () {
    $user = User::factory()->create();
    $token = $user->createToken('auth_token')->plainTextToken;

    $this->withHeader('Authorization', 'Bearer ' . $token)
        ->postJson('/logout')
        ->assertStatus(200)
        ->assertJson(['message' => 'Logout realizado com sucesso']);
});
