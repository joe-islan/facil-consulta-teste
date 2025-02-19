<?php

namespace Tests\Feature;

use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ConsultaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Medico $medico;
    private Paciente $paciente;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->medico = Medico::factory()->create();
        $this->paciente = Paciente::factory()->create();
        $this->token = Auth::guard('api')->login($this->user);
    }

    public function testAuthenticatedUserCanCreateConsulta()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos/consulta', [
            'medico_id' => $this->medico->id,
            'paciente_id' => $this->paciente->id,
            'data' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success', 'message', 'item' => ['id', 'medico_id', 'paciente_id', 'data'],
            ]);

        $this->assertDatabaseHas('consultas', [
            'medico_id' => $this->medico->id,
            'paciente_id' => $this->paciente->id,
        ]);
    }

    public function testUnauthenticatedUserCannotCreateConsulta()
    {
        Auth::logout();

        $response = $this->postJson('/api/v1/medicos/consulta', [
            'medico_id' => $this->medico->id,
            'paciente_id' => $this->paciente->id,
            'data' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(401);
    }

    public function testCannotCreateConsultaWithInvalidMedico()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos/consulta', [
            'medico_id' => 999,
            'paciente_id' => $this->paciente->id,
            'data' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['medico_id']);
    }

    public function testCannotCreateConsultaWithInvalidPaciente()
    {
        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->postJson('/api/v1/medicos/consulta', [
            'medico_id' => $this->medico->id,
            'paciente_id' => 999,
            'data' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['paciente_id']);
    }

    public function testAuthenticatedUserCanListConsultas()
    {
        Consulta::factory()->create([
            'medico_id' => $this->medico->id,
            'paciente_id' => $this->paciente->id,
            'data' => Carbon::now()->addDays(5)->format('Y-m-d H:i:s'),
        ]);

        $response = $this->withHeaders([
            'Authorization' => "Bearer $this->token",
        ])->getJson('/api/v1/consultas');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item',
            ])
            ->assertJsonCount(1, 'item');
    }

    public function testCannotCreateConflictingConsultasForSameMedico()
    {
        $data = [
            'medico_id' => $this->medico->id,
            'paciente_id' => $this->paciente->id,
            'data' => Carbon::now()->addDays(3)->format('Y-m-d H:i:s'),
        ];

        // Criando a primeira consulta
        $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->postJson('/api/v1/medicos/consulta', $data)
            ->assertStatus(201);

        // Tentando criar outra consulta no mesmo horário
        $response = $this->withHeaders(['Authorization' => "Bearer $this->token"])
            ->postJson('/api/v1/medicos/consulta', $data);

        $response->assertStatus(422)
            ->assertJson(['message' => 'O médico já possui uma consulta marcada nesse horário. Escolha um horário com pelo menos 15 minutos de diferença.']);
    }
}
