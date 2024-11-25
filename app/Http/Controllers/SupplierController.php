<?php

namespace App\Http\Controllers;

use App\Http\Requests\SupplierRequest;
use App\Manager\SupplierManager;
use App\Models\Supplier;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Rules\DocumentValidation;
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

        $supplier = $this->supplierManager->createSupplier($data);
        if ($supplier instanceof Supplier) {
            return response()->json($supplier, 201);
        } else {
            return response()->json(['message' => 'Supplier not created'], 500);
        }
        return ;
    }

    public function show($id)
    {
        $supplier = $this->supplierManager->findById($id);

        if (!$supplier) {
            return response()->json(['message' => 'Supplier not found'], 404);
        }

        return $supplier;
    }

    public function update(Request $request, Supplier $supplier)
    {
        $data = json_decode($request->getContent(), true);

        $documentType = strtoupper($data['document_type'] ?? $supplier->document_type);

        $validated = validator($data, [
            'name' => 'sometimes|string|max:255',
            'document' => ['sometimes', 'string', new DocumentValidation($documentType)],
            'document_type' => 'sometimes|string|in:CPF,CNPJ',
            'email' => 'sometimes|email|max:255',
            'phone' => 'sometimes|string|max:20',
            'street' => 'sometimes|string|max:255',
            'number' => 'sometimes|string|max:20',
            'complement' => 'sometimes|nullable|string|max:255',
            'neighborhood' => 'sometimes|string|max:255',
            'city' => 'sometimes|string|max:255',
            'state' => 'sometimes|string|size:2',
            'zip_code' => 'sometimes|string|max:8',
        ])->validate();

        return $this->supplierManager->update($supplier, $validated);
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
