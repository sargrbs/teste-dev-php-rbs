<?php

namespace App\Manager;

use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Services\BrasilApiService;
use App\Util\Constants;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierManager extends AbstractManager
{
    const PREFIX = Constants::SUPPLIER_PREFIX;
    protected SupplierRepositoryInterface $repository;
    protected BrasilApiService $brasilApi;


    public function __construct(
        SupplierRepositoryInterface $repository,
        BrasilApiService $brasilApi,
    ){
        parent::__construct(self::PREFIX);
        $this->repository = $repository;
        $this->brasilApi = $brasilApi;

    }

    public function searchSupplier(array $params): null|LengthAwarePaginator
    {
        $perPage = $params['per_page'] ?? 10;
        $page = $params['page'] ?? 1;

        $cacheKey = "search:" . md5(json_encode([
            'search' => $params['search'] ?? null,
            'page' => $page,
            'per_page' => $perPage
        ]));

        $cached = $this->getCache($cacheKey);
        if ($cached) {
            return new LengthAwarePaginator(
                collect($cached['data']),
                $cached['total'],
                $cached['per_page'],
                $cached['current_page'],
                [
                    'path' => request()->url(),
                    'query' => request()->query()
                ]
            );
        }

        $result = $this->repository->search($params);

        $this->setCache($cacheKey, [
            'data' => $result->items(),
            'total' => $result->total(),
            'per_page' => $result->perPage(),
            'current_page' => $result->currentPage(),
            'last_page' => $result->lastPage()
        ]);

        return $result;
    }

    public function createSupplier($data): string|Supplier
    {
        $cnpj = preg_replace('/[^0-9]/', '', (string) $data['document']);
        $data['document'] = $cnpj;

        if ($data['document_type'] === 'CNPJ') {
            $cnpjData = $this->brasilApi->findCnpj($data['document']);
            if ($cnpjData) {
                $data = array_merge($data, [
                    'name' => $cnpjData['razao_social'] ?? $data['name'],
                    'street' => $cnpjData['logradouro'] ?? $data['street'],
                    'number' => $cnpjData['numero'] ?? $data['number'],
                    'complement' => $cnpjData['complemento'] ?? null,
                    'neighborhood' => $cnpjData['bairro'] ?? $data['neighborhood'],
                    'city' => $cnpjData['municipio'] ?? $data['city'],
                    'state' => $cnpjData['uf'] ?? $data['state'],
                    'zip_code' => preg_replace('/\D/', '', $cnpjData['cep'] ?? $data['zip_code']),
                ]);
            }
        }

        return $this->repository->create($data);
    }

    public function findById($id): null|Supplier
    {
        $cacheKey = "id:{$id}";
        $cached = $this->getCache($cacheKey);

        if ($cached) {
            return $cached;
        }

        $supplier = $this->repository->findById($id);

        if ($supplier) {
            $this->setCache($cacheKey, $supplier);
        }

        return $supplier;
    }

    public function update(int $id, $data): Supplier
    {
        $supplier = $this->repository->findById($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $this->deleteCache("id:{$supplier->id}");
        $this->deleteCache("document:{$supplier->document}");
        $this->clearCache();

        return $this->repository->update($supplier, $data);
    }

    public function deleteSupplier(int $id): JsonResponse
    {
        $supplier = $this->repository->findById($id);

        if(!$supplier){
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        $result = $this->repository->delete($supplier);

        if ($result) {
            $this->deleteCache("id:{$id}");
            $this->deleteCache("document:{$supplier->document}");
            $this->clearCache();

            return response()->json(['message' => 'Supplier deleted successfully']);

        }

        return response()->json(['message' => 'Error deleting supplier'], 500);
    }

    public function findCnpjData(int $cnpj): array
    {
       return $this->brasilApi->findCnpj($cnpj);
    }


}
