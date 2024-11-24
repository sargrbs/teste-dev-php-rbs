<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use DatabaseTransactions;

    public function test_can_show_supplier()
    {
        $user = User::factory()->create();

        $supplier = Supplier::create([
            'name' => 'Empresa Teste',
            'document' => '09502912000136',
            'document_type' => 'CNPJ',
            'email' => 'empresa@teste.com',
            'phone' => '11999999999',
            'street' => 'Rua Teste',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '12345678'
        ]);

        $response = $this->actingAs($user)
            ->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Empresa Teste',
                'document' => '09502912000136',
            ]);
    }

    public function test_cannot_show_nonexistent_supplier()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->getJson('/api/suppliers/999');

        $response->assertStatus(404);
    }

    public function test_cannot_show_supplier_without_authentication()
    {
        $supplier = Supplier::create([
            'name' => 'Empresa Teste',
            'document' => '09502912000136',
            'document_type' => 'CNPJ',
            'email' => 'empresa@teste.com',
            'phone' => '11999999999',
            'street' => 'Rua Teste',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '12345678'
        ]);

        $response = $this->getJson("/api/suppliers/{$supplier->id}");

        $response->assertStatus(401);
    }

    public function test_can_search_suppliers()
    {
        $user = User::factory()->create();

        Supplier::create([
            'name' => 'Empresa ABC',
            'document' => '05828567000174',
            'document_type' => 'CNPJ',
            'email' => 'abc@example.com',
            'phone' => '11999999999',
            'street' => 'Rua Teste',
            'number' => '123',
            'neighborhood' => 'Centro',
            'city' => 'Curitiba',
            'state' => 'PR',
            'zip_code' => '12345678'
        ]);

        $response = $this->actingAs($user)
            ->getJson('/api/suppliers?search=ABC');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Empresa ABC');

        $response = $this->actingAs($user)
            ->getJson('/api/suppliers?search=naoexiste');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data');
    }
}
