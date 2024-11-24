<?php

namespace Tests\Unit\Repositories;

use Tests\TestCase;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use Illuminate\Support\Facades\Redis;
use Mockery;

class SupplierRepositoryTest extends TestCase
{
    protected $repository;
    protected $modelMock;

    protected function setUp(): void
    {
        parent::setUp();

        Redis::shouldReceive('setex')->andReturn(true);
        Redis::shouldReceive('get')->andReturn(null);
        Redis::shouldReceive('del')->andReturn(1);

        $this->modelMock = Mockery::mock(Supplier::class);

        $this->repository = new SupplierRepository($this->modelMock);
    }

    public function test_create_supplier()
    {
        $supplierData = [
            'name' => 'Test Supplier',
            'document' => '12345678901234',
            'document_type' => 'CNPJ',
            'email' => 'test@supplier.com',
            'phone' => '11999999999',
            'street' => 'Test Street',
            'number' => '123',
            'neighborhood' => 'Test Area',
            'city' => 'Test City',
            'state' => 'PR',
            'zip_code' => '12345678'
        ];

        $this->modelMock->shouldReceive('withTrashed->where->first')
            ->once()
            ->andReturn(null);

        $this->modelMock->shouldReceive('create')
            ->once()
            ->with(Mockery::subset($supplierData))
            ->andReturn(new Supplier($supplierData));

        $result = $this->repository->create($supplierData);

        $this->assertEquals($supplierData['name'], $result->name);
        $this->assertEquals($supplierData['document'], $result->document);
    }

    public function test_find_by_id()
    {
        $supplier = new Supplier([
            'id' => 1,
            'name' => 'Test Supplier'
        ]);

        $this->modelMock->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($supplier);

        $result = $this->repository->findById(1);

        $this->assertEquals($supplier->id, $result->id);
        $this->assertEquals($supplier->name, $result->name);
    }

    public function test_search_suppliers()
    {
        $suppliers = collect([
            new Supplier(['name' => 'Test Supplier 1']),
            new Supplier(['name' => 'Test Supplier 2'])
        ]);

        $this->modelMock->shouldReceive('query')->andReturnSelf();
        $this->modelMock->shouldReceive('where')->andReturnSelf();
        $this->modelMock->shouldReceive('paginate')
            ->once()
            ->andReturn(new \Illuminate\Pagination\LengthAwarePaginator(
                $suppliers,
                2,
                10,
                1
            ));

        $result = $this->repository->search(['search' => 'test']);

        $this->assertEquals(2, $result->total());
        $this->assertEquals('Test Supplier 1', $result->items()[0]->name);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
