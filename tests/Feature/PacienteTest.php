<?php

namespace Tests\Feature;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class PacienteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = Auth::guard('api')->login($this->user);
    }

    public function testAuthenticatedUserCanCreatePaciente()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/pacientes', [
            'nome' => 'Paciente Teste',
            'cpf' => '123.456.789-10',
            'celular' => '(11) 98765-4321',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['nome', 'cpf', 'celular'],
            ]);

        $this->assertDatabaseHas('pacientes', [
            'cpf' => '123.456.789-10',
        ]);
    }

    public function testUnauthenticatedUserCannotCreatePaciente()
    {
        Auth::logout();

        $response = $this->postJson('/api/v1/pacientes', [
            'nome' => 'Paciente Teste',
            'cpf' => '123.456.789-10',
            'celular' => '(11) 98765-4321',
        ]);

        $response->assertStatus(401);
    }

    public function testCannotCreatePacienteWithDuplicateCpf()
    {
        Paciente::factory()->create(['cpf' => '123.456.789-10']);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/pacientes', [
            'nome' => 'Outro Paciente',
            'cpf' => '123.456.789-10',
            'celular' => '(11) 98765-4321',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['cpf']);
    }

    public function testAuthenticatedUserCanListPacientes()
    {
        Paciente::factory()->count(5)->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/v1/pacientes');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item',
            ])
            ->assertJsonCount(5, 'item');
    }

    public function testAuthenticatedUserCanUpdatePaciente()
    {
        $paciente = Paciente::factory()->create([
            'nome' => 'Paciente Original',
            'celular' => '(11) 12345-6789',
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->putJson("/api/v1/pacientes/{$paciente->id}", [
            'nome' => 'Paciente Atualizado',
            'celular' => '(11) 98765-4321',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['nome', 'celular'],
            ]);

        $this->assertDatabaseHas('pacientes', [
            'id' => $paciente->id,
            'nome' => 'Paciente Atualizado',
        ]);
    }

    public function testCannotUpdatePacienteWithInvalidData()
    {
        $paciente = Paciente::factory()->create();

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->putJson("/api/v1/pacientes/{$paciente->id}", [
            'nome' => '',
            'celular' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nome', 'celular']);
    }
}
