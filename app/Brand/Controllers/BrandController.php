<?php

namespace App\Brand\Controllers;

use App\Product\Models\Product;
use App\Brand\Requests\BrandCreateRequest;
use App\Brand\Resources\BrandResource;
use App\Brand\Services\BrandService;
use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use App\User\Requests\UserUpdateRequest;
use App\User\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BrandController extends Controller
{
    protected SharedService $sharedService;
    protected BrandService $brandService;

    public function __construct(
        SharedService $sharedService,
        BrandService $brandService,
    ) {
        $this->sharedService = $sharedService;
        $this->brandService = $brandService;
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

    public function delete(Product $user): JsonResponse {
        DB::beginTransaction();
        try {
            $userValidated = $this->userService->validate($user, 'User');
            $this->userService->delete($userValidated);
            DB::commit();
            return response()->json(['message' => 'User deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function get(User $user): JsonResponse
    {
        $userValidated = $this->userService->validate($user, 'User');
        return response()->json(new UserResource($userValidated));
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

    public function update(UserUpdateRequest $request, User $user): JsonResponse
    {
        DB::beginTransaction();
        try {
            $userValidated = $this->userService->validate($user, 'User');
            $editUser = $this->prepareNewBrandData(
                $request->validated(),
            );
            $this->userService->update($userValidated, $editUser);
            DB::commit();
            return response()->json(['message' => 'User updated.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

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

    private function prepareNewBrandData(array $validatedData): array
    {
        $productData = array_merge(
            $validatedData,
            // [
            //     'password' => Hash::make('password'),
            // ],
        );
        return $this->sharedService->convertCamelToSnake($productData);
    }
}
