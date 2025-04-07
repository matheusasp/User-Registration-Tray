<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_complete_registration()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/users/complete-registration', [
            'user_id' => $user->id,
            'name' => 'Matheus Teste',
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'cpf' => '12345678901',
            'birth_date' => '1990-01-01',
        ]);
    }

    /** @test */
    public function can_list_users()
    {
        User::factory()->count(3)->create();

        $response = $this->getJson('/api/users');

        $response->assertStatus(200);
        $this->assertGreaterThanOrEqual(3, count($response->json()));
    }

    /** @test */
    public function cannot_complete_registration_with_invalid_data()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/users/complete-registration', [
            'user_id' => $user->id,
            'cpf' => '123', // CPF inválido
            'birth_date' => 'invalid-date', // data inválida
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['cpf', 'birth_date']);
    }
}
