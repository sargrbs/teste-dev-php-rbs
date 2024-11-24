<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Manager\SupplierManager;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    protected SupplierRepositoryInterface $repository;
    protected SupplierManager $supplierManager;


    public function __construct(
        SupplierRepositoryInterface $repository,
        SupplierManager $supplierManager
    ){
        $this->repository = $repository;
        $this->supplierManager = $supplierManager;
    }

    public function index(Request $request)
    {
        return $this->supplierManager->searchSupplier($request->all());
    }

    public function store(SupplierRequest $request)
    {
        $data = $request->validated();

        return $this->supplierManager->createSupplier($data);
    }

    public function show($id)
    {
        $supplier = $this->supplierManager->findById($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        return $supplier;
    }

    public function update(SupplierRequest $request, int $id)
    {
        return $this->supplierManager->update($id, $request->validated());
    }

    public function destroy(int $id)
    {
        return $this->supplierManager->deleteSupplier($id);
    }

    public function findByCnpj(Request $request)
    {
        $request->validate([
            'cnpj' => 'required|string|size:14'
        ]);

        $data = $this->supplierManager->findCnpjData($request->cnpj);

        if (!$data) {
            return response()->json(['message' => 'CNPJ not found'], 404);
        }

        return response()->json($data);
    }
}
