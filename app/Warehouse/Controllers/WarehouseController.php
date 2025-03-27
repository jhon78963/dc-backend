<?php

namespace App\Warehouse\Controllers;


use App\Shared\Controllers\Controller;
use App\Shared\Requests\GetAllRequest;
use App\Shared\Resources\GetAllCollection;
use App\Shared\Services\SharedService;
use App\Warehouse\Models\Warehouse;
use App\Warehouse\Requests\WarehouseCreateRequest;
use App\Warehouse\Requests\WarehouseUpdateRequest;
use App\Warehouse\Resources\WarehouseResource;
use App\Warehouse\Services\WarehouseService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Almacenes",
 *     description="Endpoints relacionados con almacenes."
 * )
 */
class WarehouseController extends Controller
{
    protected SharedService $sharedService;
    protected WarehouseService $warehouseService;

    public function __construct(
        SharedService $sharedService,
        WarehouseService $warehouseService,
    ) {
        $this->sharedService        = $sharedService;
        $this->warehouseService     = $warehouseService;
    }


/**
 * @OA\Post(
 *     path="/warehouses",
 *     summary="Crear un nuevo almacén",
 *     description="Crea un nuevo almacén en la base de datos.",
 *     tags={"Almacenes"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "location", "type"},
 *             @OA\Property(property="name", type="string", example="ALMACÉN CENTRAL"),
 *             @OA\Property(property="location", type="string", example="AV. PERÚ 123"),
 *             @OA\Property(property="type", type="string", enum={"PRINCIPAL", "SECUNDARIO"}, example="PRINCIPAL")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Warehouse created.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Warehouse created.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Errores de validación.",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object", example={"name": {"El nombre del almacén es obligatorio."}})
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado.")
 *         )
 *     )
 * )
 */
    public function create(WarehouseCreateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
         
            $newWarehouse = $this->prepareNewWarehouseData(
                $request->validated(),
            );
            
            $this->warehouseService->create($newWarehouse);

            DB::commit();
            return response()->json(['message' => 'Warehouse created.'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()],500);
        }
    }


/**
 * @OA\Delete(
 *     path="/warehouses/{id}",
 *     summary="Eliminar un almacén",
 *     description="Marca un almacén como eliminado en la base de datos.",
 *     tags={"Almacenes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del almacén a eliminar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Warehouse deleted.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Warehouse deleted.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Warehouse not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="No query results for model [App\\Models\\Warehouse] 1")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado.")
 *         )
 *     )
 * )
 */
    public function delete(Warehouse $warehouse): JsonResponse {
        DB::beginTransaction();
        try {
            $warehouseValidated = $this->warehouseService->validate($warehouse, 'Warehouse');
            $this->warehouseService->delete($warehouseValidated);
            DB::commit();
            return response()->json(['message' => 'Warehouse deleted.']);
        } catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()]);
        }
    }

/**
 * @OA\Get(
 *     path="/warehouses/{id}",
 *     summary="Obtener información de un almacén",
 *     description="Retorna los detalles de un almacén específico basado en su ID.",
 *     tags={"Almacenes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del almacén a consultar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles del almacén.",
 *         @OA\JsonContent(ref="#/components/schemas/WarehouseResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Warehouse not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="No query results for model [App\\Models\\Warehouse] 1")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado.")
 *         )
 *     )
 * )
 */
    public function get(Warehouse $warehouse): JsonResponse
    {
        try {
            $warehouseValidated = $this->warehouseService->validate($warehouse, 'Warehouse');
            return response()->json(new WarehouseResource($warehouseValidated),200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


/**
 * @OA\Get(
 *     path="/warehouses",
 *     summary="Obtener la lista de almacenes",
 *     description="Retorna una lista paginada de almacenes, con opciones de filtrado y ordenamiento.",
 *     tags={"Almacenes"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Número de página para la paginación",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         description="Cantidad de elementos por página",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Parameter(
 *         name="sort_by",
 *         in="query",
 *         description="Campo por el cual ordenar",
 *         @OA\Schema(type="string", example="name")
 *     ),
 *     @OA\Parameter(
 *         name="order",
 *         in="query",
 *         description="Orden de clasificación (ascendente o descendente)",
 *         @OA\Schema(type="string", enum={"asc", "desc"}, example="asc")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de almacenes paginada.",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/WarehouseResource")),
 *             @OA\Property(property="total", type="integer", example=100),
 *             @OA\Property(property="pages", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado.")
 *         )
 *     )
 * )
 */
    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'Warehouse',
            'Warehouse',
            'name'
        );

        return response()->json(new GetAllCollection(
            WarehouseResource::collection(resource: $query['collection']),
            $query['total'],
            $query['pages'],
        ),200);
    }

/**
 * @OA\Patch(
 *     path="/warehouses/{id}",
 *     summary="Actualizar un almacén",
 *     description="Actualiza la información de un almacén existente basado en su ID.",
 *     tags={"Almacenes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del almacén a actualizar",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "location", "type"},
 *             @OA\Property(property="name", type="string", example="ALMACÉN ACTUALIZADO"),
 *             @OA\Property(property="location", type="string", example="AV. PERÚ 456"),
 *             @OA\Property(property="type", type="string", example="PRINCIPAL", enum={"PRINCIPAL", "SECUNDARIO"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Almacén actualizado correctamente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Warehouse updated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Solicitud incorrecta debido a errores de validación.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error de validación.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Almacén no encontrado.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="No query results for model [App\\Models\\Warehouse] 1")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor.",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado.")
 *         )
 *     )
 * )
 */
    public function update(WarehouseUpdateRequest $request, Warehouse $warehouse): JsonResponse
    {   
        //return response()->json($request->all()['name']);
        DB::beginTransaction();
        try {
            $warehouseValidated = $this->warehouseService->validate($warehouse, 'Warehouse');
            $editWarehouse      = $this->prepareNewWarehouseData(
                $request->validated(),
            );
            $this->warehouseService->update($warehouseValidated, $editWarehouse);
            DB::commit();
            return response()->json(['message' => 'Warehouse updated.']);
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

    private function prepareNewWarehouseData(array $validatedData): array
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
