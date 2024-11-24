<?php

namespace App\Repositories;

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Util\Constants;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class SupplierRepository implements SupplierRepositoryInterface
{
    protected $model;
    protected $prefix = Constants::SUPPLIER_PREFIX;

    public function __construct(Supplier $model)
    {
        $this->model = $model;
    }

    public function findById(int $id): ?Supplier
    {
        $supplier = $this->model->find($id);

        return $supplier;
    }

    public function search(array $params): LengthAwarePaginator
    {
        $perPage = $params['per_page'] ?? 10;
        $page = $params['page'] ?? 1;

        $query = $this->model->query();

        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('document', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $result = $query->paginate($perPage, ['*'], 'page', $page);

        return $result;
    }

    public function create(array $data): Supplier
    {
        $existingSupplier = $this->model->withTrashed()
            ->where('document', $data['document'])
            ->first();

        if ($existingSupplier) {
            if ($existingSupplier->trashed()) {
                $existingSupplier->restore();
                $existingSupplier->update($data);

                $this->clearCache();

                return $existingSupplier;
            }

            throw new \Exception('Fornecedor já cadastrado com este documento.');
        }

        $supplier = $this->model->create($data);
        $this->clearCache();

        return $supplier;
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);

        return $supplier;
    }

    public function delete(Supplier $supplier): bool
    {
        $result = $supplier->delete();

        return $result;
    }

    protected function clearCache(): void
    {
        try {
            $pattern = $this->prefix . '*';
            $iterator = null;
            $keys = [];

            do {
                $keys = Redis::scan($iterator, [
                    'match' => $pattern,
                    'count' => 100
                ]);

                if ($keys) {
                    foreach ($keys as $key) {
                        Redis::del($key);
                    }
                }
            } while ($iterator > 0);

        } catch (\Exception $e) {
            Log::error('Redis clear error: ' . $e->getMessage());
        }
    }

}
