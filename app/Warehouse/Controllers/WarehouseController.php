<?php

namespace App\Warehouse\Controllers;

use App\Brand\Models\Brand;
use App\Product\Models\Product;
use App\Brand\Requests\BrandCreateRequest;
use App\Brand\Requests\BrandUpdateRequest;
use App\Brand\Resources\BrandResource;
use App\Brand\Services\BrandService;
use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use App\User\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WarehouseController extends Controller
{
    protected SharedService $sharedService;
    protected BrandService $warehouseService;

    public function __construct(
        SharedService $sharedService,
        BrandService $brandService,
    ) {
        $this->sharedService    = $sharedService;
        $this->brandService     = $brandService;
    }
    public function create(BrandCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
         
            $newBrand = $this->prepareNewBrandData(
                $request->validated(),
            );
            
            $this->brandService->create($newBrand);

            DB::commit();
            return response()->json(['message' => 'Brand created.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function delete(Brand $brand): JsonResponse {
        DB::beginTransaction();
        try {
            $brandValidated = $this->brandService->validate($brand, 'Brand');
            $this->brandService->delete($brandValidated);
            DB::commit();
            return response()->json(['message' => 'Brand deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function get(Brand $brand): JsonResponse
    {
        $brandValidated = $this->brandService->validate($brand, 'Brand');
        return response()->json(new BrandResource($brandValidated));
    }

    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'Brand',
            'Brand',
            'name'
        );

        return response()->json(new GetAllCollection(
            BrandResource::collection(resource: $query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }

    public function update(BrandUpdateRequest $request, Brand $brand): JsonResponse
    {
        DB::beginTransaction();
        try {
            $brandValidated = $this->brandService->validate($brand, 'Brand');
            $editBrand      = $this->prepareNewBrandData(
                $request->validated(),
            );
            $this->brandService->update($brandValidated, $editBrand);
            DB::commit();
            return response()->json(['message' => 'Brand updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    /*
    private function generateErrorResponse(bool $emailExists, bool $usernameExists): ?array
    {
        $errors = [];

        if ($emailExists) {
            $errors['email'] = 'El email ya existe.';
        }

        if ($usernameExists) {
            $errors['username'] = 'El username ya existe.';
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'message' => 'El email y/o username ya existen.',
                'errors' => $errors
            ];
        }

        return null;
    }
    */

    private function prepareNewBrandData(array $validatedData): array
    {
        $brandData = array_merge(
            $validatedData,
            // [
            //     'password' => Hash::make('password'),
            // ],
        );
        return $this->sharedService->convertCamelToSnake($brandData);
    }
}
