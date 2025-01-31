<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Cidade;

class CidadeTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_cities()
    {
        Cidade::factory()->count(5)->create();

        $response = $this->getJson('/api/v1/cidades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item'
            ])
            ->assertJsonCount(5, 'item');
    }

    public function test_can_search_city_by_name()
    {
        Cidade::factory()->create(['nome' => 'São Paulo']);
        Cidade::factory()->create(['nome' => 'Rio de Janeiro']);

        $response = $this->getJson('/api/v1/cidades?nome=São');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item'
            ])
            ->assertJsonCount(1, 'item')
            ->assertJsonFragment(['nome' => 'São Paulo']);
    }

    public function test_city_list_is_ordered_alphabetically()
    {
        Cidade::factory()->create(['nome' => 'Curitiba']);
        Cidade::factory()->create(['nome' => 'Belo Horizonte']);
        Cidade::factory()->create(['nome' => 'Natal']);

        $response = $this->getJson('/api/v1/cidades');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success', 'message', 'item'
            ])
            ->assertSeeInOrder(['Belo Horizonte', 'Curitiba', 'Natal']);
    }
}
