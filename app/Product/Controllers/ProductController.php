<?php

namespace App\Product\Controllers;

use App\MeasurementUnit\Models\MeasurementUnit;
use App\Product\Models\Product;
use App\Product\Requests\ProductCreateRequest;
use App\Product\Resources\ProductResource;
use App\Product\Services\ProductService;
use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use App\product\Requests\productUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProductController extends Controller
{
    protected SharedService $sharedService;
    protected ProductService $productService;

    public function __construct(
        SharedService $sharedService,
        ProductService $productService,
    ) {
        $this->sharedService = $sharedService;
        $this->productService = $productService;
    }

    public function create(ProductCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
         
            $newProduct = $this->prepareNewProductData(
                $request->validated(),
            );
            
            $this->productService->create($newProduct);

            DB::commit();
            return response()->json(['message' => 'Product created.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function delete(Product $product): JsonResponse {
        DB::beginTransaction();
        try {
            $productValidated = $this->productService->validate($product, 'Product');
            $this->productService->delete($productValidated);
            DB::commit();
            return response()->json(['message' => 'Product deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function get(Product $product): JsonResponse
    {
        $productValidated = $this->productService->validate($product, 'Product');
        return response()->json(new ProductResource($productValidated));
    }

    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'Product',
            'Product',
            'name'
        );

        return response()->json(new GetAllCollection(
            ProductResource::collection(resource: $query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }

    public function update(productUpdateRequest $request, product $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            $productValidated = $this->productService->validate($product, 'product');
            $editProduct = $this->prepareNewProductData(
                $request->validated(),
            );
            $this->productService->update($productValidated, $editProduct);
            DB::commit();
            return response()->json(['message' => 'Product updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    private function generateErrorResponse(bool $emailExists, bool $productnameExists): ?array
    {
        $errors = [];

        if ($emailExists) {
            $errors['email'] = 'El email ya existe.';
        }

        if ($productnameExists) {
            $errors['productname'] = 'El productname ya existe.';
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'El email y/o productname ya existen.',
                'errors' => $errors
            ];
        }

        return null;
    }

    private function prepareNewProductData(array $validatedData): array
    {
        $productData = array_merge(
            $validatedData,
            [
                'measurementUnitName' => MeasurementUnit::find($validatedData['measurementUnitId'])->name,
            ],
        );

        return $this->sharedService->convertCamelToSnake($productData);
    }
}
