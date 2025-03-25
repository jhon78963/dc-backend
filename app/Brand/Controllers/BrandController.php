<?php

namespace App\Brand\Controllers;

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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


/**
 * @OA\Info(
 *     title="API DC",
 *     version="1.0.0",
 *     description="Endpoints"
 * )
 *
 * @OA\Server(
 *     url="http://127.0.0.1:8000",
 *     description="Servidor local"
 * )
 *
 *  @OA\Tag(
 *     name="Marcas",
 *     description="Endpoints relacionados con marcas"
 * )
 */


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



/**
 * @OA\Post(
 *     path="/api/brands",
 *     summary="Crear una nueva marca",
 *     description="Este endpoint permite crear una nueva marca en el sistema.",
 *     operationId="createBrand",
 *     tags={"Marcas"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Nike")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Marca creada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Brand created.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array", @OA\Items(type="string", example="El nombre de la marca ya está en uso."))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error inesperado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error en el servidor.")
 *         )
 *     )
 * )
 */

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
            return response()->json(['error' =>  $e->getMessage()], 500);
        }
    }


/**
 * @OA\Delete(
 *     path="/api/brands/{brand}",
 *     summary="Eliminar una marca",
 *     description="Elimina una marca específica por su ID.",
 *     operationId="deleteBrand",
 *     tags={"Marcas"},
 *     @OA\Parameter(
 *         name="brand",
 *         in="path",
 *         required=true,
 *         description="ID de la marca a eliminar",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Marca eliminada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Brand deleted.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error en la solicitud",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Mensaje de error.")
 *         )
 *     )
 * )
 */
    public function delete(Brand $brand): JsonResponse {
        DB::beginTransaction();
        try {
            $brandValidated = $this->brandService->validate($brand, 'Brand');
            $this->brandService->delete($brandValidated);
            DB::commit();
            return response()->json(['message' => 'Brand deleted.'],200);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()],500);
        }
    }


/**
 * @OA\Get(
 *     path="/api/brands/{brand}",
 *     summary="Obtener una marca",
 *     description="Devuelve los detalles de una marca específica.",
 *     operationId="getBrand",
 *     tags={"Marcas"},
 *     @OA\Parameter(
 *         name="brand",
 *         in="path",
 *         required=true,
 *         description="ID de la marca a obtener",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Marca obtenida exitosamente",
 *         @OA\JsonContent(ref="#/components/schemas/BrandResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Marca no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Brand not found.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unexpected server error.")
 *         )
 *     )
 * )
 */
    public function get(Brand $brand): JsonResponse
    {
        try {
            $brandValidated = $this->brandService->validate($brand, 'Brand');
            return response()->json(new BrandResource($brandValidated), 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
 * @OA\Get(
 *     path="/api/brands",
 *     summary="Obtener todas las marcas",
 *     description="Devuelve una lista paginada de marcas.",
 *     operationId="getAllBrands",
 *     tags={"Marcas"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="Número de la página",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Cantidad de resultados por página",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de marcas obtenida exitosamente",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/BrandResource")
 *             ),
 *             @OA\Property(property="total", type="integer", example=100),
 *             @OA\Property(property="pages", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Unexpected server error.")
 *         )
 *     )
 * )
 */
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


    /**
 * @OA\Patch(
 *     path="/api/brands/{id}",
 *     summary="Actualizar parcialmente una marca",
 *     description="Permite actualizar uno o más campos de una marca existente.",
 *     operationId="updateBrand",
 *     tags={"Marcas"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la marca a actualizar",
 *         required=true,
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Samsung")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Marca actualizada exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Brand updated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Marca no encontrada",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Marca no encontrada.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El nombre de la marca ya está en uso.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al actualizar la marca.")
 *         )
 *     )
 * )
 */
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
            return response()->json(['message' => 'Brand updated.'],200);
        }catch (ModelNotFoundException $e) {
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
