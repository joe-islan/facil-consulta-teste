<?php

namespace Tests\Feature;

use App\Models\Cidade;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CidadeTest extends TestCase
{
    use RefreshDatabase;

    public function testCanListCities()
    {
        Cidade::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/cidades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item',
            ])
            ->assertJsonCount(5, 'item');
    }

    public function testCanSearchCityByName()
    {
        Cidade::factory()->create(['nome' => 'São Paulo']);
        Cidade::factory()->create(['nome' => 'Rio de Janeiro']);

        $response = $this->getJson('/api/v1/cidades?nome=São');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item',
            ])
            ->assertJsonCount(1, 'item')
            ->assertJsonFragment(['nome' => 'São Paulo']);
    }

    public function testCityListIsOrderedAlphabetically()
    {
        Cidade::factory()->create(['nome' => 'Curitiba']);
        Cidade::factory()->create(['nome' => 'Belo Horizonte']);
        Cidade::factory()->create(['nome' => 'Natal']);

        $response = $this->getJson('/api/v1/cidades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item',
            ])
            ->assertSeeInOrder(['Belo Horizonte', 'Curitiba', 'Natal']);
    }
}
