<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Medico;
use App\Models\Cidade;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MedicoTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Cidade $cidade;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->cidade = Cidade::factory()->create();
        $this->token = Auth::guard('api')->login($this->user);
    }

    public function test_can_list_medicos()
    {
        Medico::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/medicos');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item'
            ])
            ->assertJsonCount(3, 'item');
    }

    public function test_can_filter_medicos_by_city()
    {
        $cidade1 = Cidade::factory()->create(['nome' => 'São Paulo']);
        $cidade2 = Cidade::factory()->create(['nome' => 'Rio de Janeiro']);

        Medico::factory()->create(['cidade_id' => $cidade1->id, 'nome' => 'Dr. João']);
        Medico::factory()->create(['cidade_id' => $cidade2->id, 'nome' => 'Dr. Maria']);

        $response = $this->getJson("/api/v1/cidades/{$cidade1->id}/medicos");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item'
            ])
            ->assertJsonCount(1, 'item')
            ->assertJsonFragment(['nome' => 'Dr. João']);
    }

    public function test_authenticated_user_can_create_medico()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos', [
            'nome' => 'Dr. Teste',
            'especialidade' => 'Cardiologista',
            'cidade_id' => $this->cidade->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['nome', 'especialidade', 'cidade_id']
            ]);

        $this->assertDatabaseHas('medicos', [
            'nome' => 'Dr. Teste',
            'especialidade' => 'Cardiologista',
            'cidade_id' => $this->cidade->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_create_medico()
    {
        Auth::logout();
        
        $response = $this->postJson('/api/v1/medicos', [
            'nome' => 'Dr. Teste',
            'especialidade' => 'Cardiologista',
            'cidade_id' => $this->cidade->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_cannot_create_medico_with_missing_fields()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos', [
            'especialidade' => 'Ortopedista',
            'cidade_id' => $this->cidade->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome']);
    }

    public function test_cannot_create_medico_with_invalid_cidade()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos', [
            'nome' => 'Dr. Teste',
            'especialidade' => 'Pediatra',
            'cidade_id' => 999,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cidade_id']);
    }
}
