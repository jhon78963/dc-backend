<?php

namespace App\Category\Controllers;

use App\Category\Models\Category;
use App\Category\Requests\CategoryCreateRequest;
use App\Category\Requests\CategoryUpdateRequest;
use App\Category\Resources\CategoryResource;
use App\Category\Services\CategoryService;
use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    protected SharedService $sharedService;
    protected CategoryService $categoryService;

    public function __construct(
        SharedService $sharedService,
        CategoryService $categoryService,
    ) {
        $this->sharedService    = $sharedService;
        $this->categoryService  = $categoryService;
    }
    public function create(CategoryCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
         
            $newCategory = $this->prepareNewCategoryData(
                $request->validated(),
            );
            
            $this->categoryService->create($newCategory);

            DB::commit();
            return response()->json(['message' => 'Category created.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function delete(Category $Category): JsonResponse {
        DB::beginTransaction();
        try {
            $categoryValidated = $this->categoryService->validate($Category, 'Category');
            $this->categoryService->delete($categoryValidated);
            DB::commit();
            return response()->json(['message' => 'Category deleted.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

    public function get(Category $category): JsonResponse
    {
        $categoryValidated = $this->categoryService->validate($category, 'Category');
        return response()->json(new CategoryResource($categoryValidated));
    }

    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'Category',
            'Category',
            'name'
        );

        return response()->json(new GetAllCollection(
            CategoryResource::collection(resource: $query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }

    public function update(CategoryUpdateRequest $request, Category $category): JsonResponse
    {
        DB::beginTransaction();
        try {
            $categoryValidated  = $this->categoryService->validate($category, 'Category');
            $editCategory       = $this->prepareNewCategoryData(
                $request->validated(),
            );
            $this->categoryService->update($categoryValidated, $editCategory);
            DB::commit();
            return response()->json(['message' => 'Category updated.']);
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

    private function prepareNewCategoryData(array $validatedData): array
    {
        $categoryData = array_merge(
            $validatedData,
            // [
            //     'password' => Hash::make('password'),
            // ],
        );
        return $this->sharedService->convertCamelToSnake($categoryData);
    }
}
