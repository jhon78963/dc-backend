<?php

namespace App\MeasurementUnit\Controllers;

use App\Brand\Models\Brand;
use App\Product\Models\Product;
use App\Brand\Requests\BrandCreateRequest;
use App\Brand\Requests\BrandUpdateRequest;
use App\Brand\Resources\BrandResource;
use App\MeasurementUnit\Resources\MeasurementUnitResource;
use App\MeasurementUnit\Services\MeasurementUnitService;
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
 * @OA\Tag(
 *     name="Unidades de medida",
 *     description="Endpoints relacionados con unidades de medida."
 * )
 */
class MeasurementUnitController extends Controller
{
    protected SharedService $sharedService;
    protected MeasurementUnitService $measurementUnitService;



    public function __construct(
        SharedService $sharedService,
        MeasurementUnitService $measurementUnitService,
    ) {
        $this->sharedService            = $sharedService;
        $this->measurementUnitService   = $measurementUnitService;
    }


/**
 * @OA\Get(
 *     path="/api/measurement_units",
 *     summary="Obtener todas las unidades de medida",
 *     description="Devuelve una lista paginada de todas las unidades de medida.",
 *     operationId="getAllMeasurementUnits",
 *     tags={"Unidades de medida"},
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
 *         description="Cantidad de unidades de medida por página",
 *         required=false,
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de unidades de medida obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/MeasurementUnitResource")
 *             ),
 *             @OA\Property(property="total", type="integer", example=50),
 *             @OA\Property(property="pages", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al obtener unidades de medida.")
 *         )
 *     )
 * )
 */
    public function getAll(GetAllRequest $request): JsonResponse
    {
        $query = $this->sharedService->query(
            $request,
            'MeasurementUnit',
            'MeasurementUnit',
            'name'
        );

        return response()->json(new GetAllCollection(
            MeasurementUnitResource::collection(resource: $query['collection']),
            $query['total'],
            $query['pages'],
        ));
    }


}
