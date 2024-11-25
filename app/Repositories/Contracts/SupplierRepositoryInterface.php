<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;
use Illuminate\Pagination\LengthAwarePaginator;

interface SupplierRepositoryInterface
{
    public function findById(int $id): ?Supplier;
    public function search(array $params): LengthAwarePaginator;
    public function create(array $data): string|Supplier;
    public function update(Supplier $supplier, array $data): Supplier;
    public function delete(Supplier $supplier): bool;
}
