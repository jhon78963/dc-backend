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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Tag(
 *     name="Categorías",
 *     description="Endpoints relacionados con categorías."
 * )
 */
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


/**
 * @OA\Post(
 *     path="/api/categories",
 *     summary="Crear una nueva categoría",
 *     description="Crea una nueva categoría con los datos proporcionados.",
 *     operationId="createCategory",
 *     tags={"Categorías"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Electrónica")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Categoría creada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category created.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El nombre de la categoría es obligatorio."),
 *                     @OA\Items(type="string", example="El nombre de la categoría debe ser una cadena de texto."),
 *                     @OA\Items(type="string", example="El nombre de la categoría no debe exceder los 120 caracteres."),
 *                     @OA\Items(type="string", example="El nombre de la categoría ya está en uso.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al crear la categoría.")
 *         )
 *     )
 * )
 */
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
            return response()->json(['error' =>  $e->getMessage()],500);
        }
    }


    /**
 * @OA\Delete(
 *     path="/api/categories/{id}",
 *     summary="Eliminar una categoría",
 *     description="Elimina una categoría por su ID.",
 *     operationId="deleteCategory",
 *     tags={"Categorías"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la categoría a eliminar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categoría eliminada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category deleted.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Categoría no encontrada.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al eliminar la categoría.")
 *         )
 *     )
 * )
 */

    public function delete(Category $Category): JsonResponse {
        DB::beginTransaction();
        try {
            $categoryValidated = $this->categoryService->validate($Category, 'Category');
            $this->categoryService->delete($categoryValidated);
            DB::commit();
            return response()->json(['message' => 'Category deleted.'],200);
        }catch (ModelNotFoundException $e) { 
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()],500);
        }
    }


/**
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     summary="Obtener una categoría",
 *     description="Obtiene los detalles de una categoría específica por su ID.",
 *     operationId="getCategory",
 *     tags={"Categorías"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la categoría a obtener",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles de la categoría",
 *         @OA\JsonContent(ref="#/components/schemas/CategoryResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Categoría no encontrada.")
 *         )
 *     ),
*      @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Ocurrió un error inesperado.")
 *         )
 *     )
 * )
 */
    public function get(Category $category): JsonResponse
    {
        try {
            $categoryValidated = $this->categoryService->validate($category, 'Category');
            return response()->json(new CategoryResource($categoryValidated), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



/**
 * @OA\Get(
 *     path="/api/categories",
 *     summary="Obtener todas las categorías",
 *     description="Devuelve una lista paginada de todas las categorías.",
 *     operationId="getAllCategories",
 *     tags={"Categorías"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Número de página",
 *         required=false,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Cantidad de categorías por página",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de categorías obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/CategoryResource")
 *             ),
 *             @OA\Property(property="total", type="integer", example=50),
 *             @OA\Property(property="pages", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al obtener categorías.")
 *         )
 *     )
 * )
 */
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


/**
 * @OA\Patch(
 *     path="/api/categories/{id}",
 *     summary="Actualizar parcialmente una categoría",
 *     description="Permite actualizar uno o más campos de una categoría existente.",
 *     operationId="updateCategory",
 *     tags={"Categorías"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la categoría a actualizar",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Electrodomésticos")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Categoría actualizada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Category updated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Categoría no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Categoría no encontrada.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El nombre de la categoría ya está en uso.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al actualizar la categoría.")
 *         )
 *     )
 * )
 */
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
            return response()->json(['message' => 'Category updated.'],200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()],500);
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
