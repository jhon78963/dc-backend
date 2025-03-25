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
use App\product\Requests\ProductUpdateRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


/**
 * @OA\Tag(
 *     name="Productos",
 *     description="Endpoints relacionados con productos."
 * )
 */
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


/**
 * @OA\Post(
 *     path="/api/products",
 *     summary="Crear un nuevo producto",
 *     description="Crea un nuevo producto con los datos proporcionados.",
 *     operationId="createProduct",
 *     tags={"Productos"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"id", "brand_id", "category_id", "measurement_unit_id", "name", "sale_price", "purchase_price", "minimum_stock"},
 *             @OA\Property(property="id", type="integer", example=101),
 *             @OA\Property(property="brand_id", type="integer", example=5),
 *             @OA\Property(property="category_id", type="integer", example=2),
 *             @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *             @OA\Property(property="measurement_unit_name", type="string", example="Unidad"),
 *             @OA\Property(property="name", type="string", example="Laptop Gamer"),
 *             @OA\Property(property="barcode", type="string", example="1234567890123"),
 *             @OA\Property(property="barcode_path", type="string", example="/images/barcodes/1234567890123.png"),
 *             @OA\Property(property="sale_price", type="number", format="float", example=1499.99),
 *             @OA\Property(property="purchase_price", type="number", format="float", example=1200.50),
 *             @OA\Property(property="minimum_stock", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Producto creado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Product created.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                
 *                 @OA\Property(property="brand_id", type="array",
 *                     @OA\Items(type="string", example="El ID de la marca es obligatorio."),
 *                     @OA\Items(type="string", example="El ID de la marca debe ser un número entero.")
 *                 ),
 *                 @OA\Property(property="category_id", type="array",
 *                     @OA\Items(type="string", example="El ID de la categoría es obligatorio."),
 *                     @OA\Items(type="string", example="El ID de la categoría debe ser un número entero.")
 *                 ),
 *                 @OA\Property(property="measurement_unit_id", type="array",
 *                     @OA\Items(type="string", example="El ID de la unidad de medida es obligatorio."),
 *                     @OA\Items(type="string", example="El ID de la unidad de medida debe ser un número entero.")
 *                 ),
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El nombre del producto es obligatorio."),
 *                     @OA\Items(type="string", example="El nombre del producto debe ser una cadena de texto.")
 *                 ),
 *                 @OA\Property(property="sale_price", type="array",
 *                     @OA\Items(type="string", example="El precio de venta es obligatorio."),
 *                     @OA\Items(type="string", example="El precio de venta debe ser un número válido.")
 *                 ),
 *                 @OA\Property(property="purchase_price", type="array",
 *                     @OA\Items(type="string", example="El precio de compra es obligatorio."),
 *                     @OA\Items(type="string", example="El precio de compra debe ser un número válido.")
 *                 ),
 *                 @OA\Property(property="minimum_stock", type="array",
 *                     @OA\Items(type="string", example="El stock mínimo es obligatorio."),
 *                     @OA\Items(type="string", example="El stock mínimo debe ser un número entero.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al crear el producto.")
 *         )
 *     )
 * )
 */
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
            return response()->json(['error' =>  $e->getMessage()],500);
        }
    }


    /**
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Eliminar un producto",
 *     description="Elimina un producto existente por su ID.",
 *     operationId="deleteProduct",
 *     tags={"Productos"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del producto a eliminar",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Producto eliminado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Product deleted.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Producto no encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Producto no encontrado.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al eliminar el producto.")
 *         )
 *     )
 * )
 */
    public function delete(Product $product): JsonResponse {
        DB::beginTransaction();
        try {
            $productValidated = $this->productService->validate($product, 'Product');
            $this->productService->delete($productValidated);
            DB::commit();
            return response()->json(['message' => 'Product deleted.'],200);
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
 *     path="/api/products/{id}",
 *     summary="Obtener un producto",
 *     description="Obtiene los detalles de un producto específico por su ID.",
 *     operationId="getProduct",
 *     tags={"Productos"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del producto a obtener",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalles del producto",
 *         @OA\JsonContent(ref="#/components/schemas/ProductResource")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Producto no encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Producto no encontrado.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al obtener el producto.")
 *         )
 *     )
 * )
 */
    public function get(Product $product): JsonResponse
    {
        try {
            $productValidated = $this->productService->validate($product, 'Product');
            return response()->json(new ProductResource($productValidated));
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    /**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Obtener todos los productos",
 *     description="Obtiene una lista paginada de todos los productos.",
 *     operationId="getAllProducts",
 *     tags={"Productos"},
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         required=false,
 *         description="Número de página para la paginación",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Parameter(
 *         name="per_page",
 *         in="query",
 *         required=false,
 *         description="Cantidad de productos por página",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Lista de productos obtenida exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array",
 *                 @OA\Items(ref="#/components/schemas/ProductResource")
 *             ),
 *             @OA\Property(property="total", type="integer", example=100),
 *             @OA\Property(property="pages", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al obtener la lista de productos.")
 *         )
 *     )
 * )
 */
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


/**
 * @OA\Patch(
 *     path="/api/products/{id}",
 *     summary="Actualizar un producto",
 *     description="Actualiza los datos de un producto existente por su ID.",
 *     operationId="updateProduct",
 *     tags={"Productos"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID del producto a actualizar",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"brand_id", "category_id", "measurement_unit_id", "name", "sale_price", "purchase_price", "minimum_stock"},
 *             @OA\Property(property="brand_id", type="integer", example=5),
 *             @OA\Property(property="category_id", type="integer", example=2),
 *             @OA\Property(property="measurement_unit_id", type="integer", example=1),
 *             @OA\Property(property="measurement_unit_name", type="string", example="Unidad"),
 *             @OA\Property(property="name", type="string", example="Laptop Gamer"),
 *             @OA\Property(property="barcode", type="string", example="1234567890123"),
 *             @OA\Property(property="barcode_path", type="string", example="/images/barcodes/1234567890123.png"),
 *             @OA\Property(property="sale_price", type="number", format="float", example=1499.99),
 *             @OA\Property(property="purchase_price", type="number", format="float", example=1200.50),
 *             @OA\Property(property="minimum_stock", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Producto actualizado exitosamente",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Product updated.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Error de validación",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="brand_id", type="array",
 *                     @OA\Items(type="string", example="El campo brand_id es obligatorio.")
 *                 ),
 *                 @OA\Property(property="category_id", type="array",
 *                     @OA\Items(type="string", example="El campo category_id es obligatorio.")
 *                 ),
 *                 @OA\Property(property="measurement_unit_id", type="array",
 *                     @OA\Items(type="string", example="El campo measurement_unit_id es obligatorio.")
 *                 ),
 *                 @OA\Property(property="name", type="array",
 *                     @OA\Items(type="string", example="El campo name es obligatorio."),
 *                     @OA\Items(type="string", example="El campo name debe ser una cadena de texto.")
 *                 ),
 *                 @OA\Property(property="sale_price", type="array",
 *                     @OA\Items(type="string", example="El campo sale_price es obligatorio."),
 *                     @OA\Items(type="string", example="El campo sale_price debe ser un número.")
 *                 ),
 *                 @OA\Property(property="purchase_price", type="array",
 *                     @OA\Items(type="string", example="El campo purchase_price es obligatorio."),
 *                     @OA\Items(type="string", example="El campo purchase_price debe ser un número.")
 *                 ),
 *                 @OA\Property(property="minimum_stock", type="array",
 *                     @OA\Items(type="string", example="El campo minimum_stock es obligatorio."),
 *                     @OA\Items(type="string", example="El campo minimum_stock debe ser un número entero.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Producto no encontrado",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Producto no encontrado.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error interno del servidor",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Error inesperado al actualizar el producto.")
 *         )
 *     )
 * )
 */
    public function update(ProductUpdateRequest $request, product $product): JsonResponse
    {
        DB::beginTransaction();
        try {
            $productValidated = $this->productService->validate($product, 'product');
            $editProduct = $this->prepareNewProductData(
                $request->validated(),
            );
            $this->productService->update($productValidated, $editProduct);
            DB::commit();
            return response()->json(['message' => 'Product updated.'],200);
        }catch (ModelNotFoundException $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 404);
        }catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' =>  $e->getMessage()],500);
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
