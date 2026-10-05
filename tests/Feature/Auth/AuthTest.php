<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('*');
});

const TEST_PASSWORD = 'Xy9#mK2$pLq8';

it('regista um novo user e devolve token', function () {
    $response = $this->postJson('/api/register', [
        'name'                  => 'João Silva',
        'email'                 => 'joao@example.com',
        'password'              => TEST_PASSWORD,
        'password_confirmation' => TEST_PASSWORD,
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user'         => ['id', 'name', 'email'],
            'access_token',
            'token_type',
        ]);

    $this->assertDatabaseHas('users', [
        'email' => 'joao@example.com',
    ]);
});

it('não regista com email duplicado', function () {
    User::factory()->create(['email' => 'existente@example.com']);

    $response = $this->postJson('/api/register', [
        'name'                  => 'Outro',
        'email'                 => 'existente@example.com',
        'password'              => TEST_PASSWORD,
        'password_confirmation' => TEST_PASSWORD,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('não regista com password fraca ou sem confirmação', function () {
    $response = $this->postJson('/api/register', [
        'name'     => 'João',
        'email'    => 'joao@example.com',
        'password' => '123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});

it('faz login com credenciais válidas', function () {
    $user = User::factory()->create([
        'email'    => 'joao@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email'    => 'joao@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure(['user', 'access_token', 'token_type']);
});

it('não faz login com password errada', function () {
    User::factory()->create([
        'email'    => 'joao@example.com',
        'password' => Hash::make('password123'),
    ]);

    $response = $this->postJson('/api/login', [
        'email'    => 'joao@example.com',
        'password' => 'errada',
    ]);

    $response->assertStatus(401)
        ->assertJson(['message' => 'Credenciais inválidas']);
});

it('faz logout e invalida o token', function () {
    $user  = User::factory()->create();
    $token = $user->createToken('test')->plainTextToken;

    // 1. Logout com o token real
    $this->withToken($token)
        ->postJson('/api/logout')
        ->assertStatus(200);

    // 2. Esquece o guard — força o Laravel a reautenticar
    $this->app['auth']->forgetGuards();

    // 3. Token já não funciona
    $this->withToken($token)
        ->getJson('/api/me')
        ->assertStatus(401);
});

it('devolve 429 após demasiadas tentativas de login', function () {
    $user = User::factory()->create(['email' => 'joao@example.com']);

    for ($i = 0; $i < 6; $i++) {
        $this->postJson('/api/login', [
            'email'    => 'joao@example.com',
            'password' => 'errada',
        ]);
    }

    $this->postJson('/api/login', [
        'email'    => 'joao@example.com',
        'password' => 'errada',
    ])->assertStatus(429);
});
