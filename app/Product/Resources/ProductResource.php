<?php

namespace App\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="ProductResource",
 *     type="object",
 *     title="Product Resource",
 *     description="Estructura del recurso de un producto",
 *     required={"id", "brand_id", "category_id", "measurement_unit_id", "name", "sale_price", "purchase_price", "minimum_stock"},
 *     @OA\Property(property="id", type="integer", example=101 , nullable=false),
 *     @OA\Property(property="brand_id", type="integer", example=5, nullable=false),
 *     @OA\Property(property="category_id", type="integer", example=2, nullable=false),
 *     @OA\Property(property="measurement_unit_id", type="integer", example=1, nullable=false),
 *     @OA\Property(property="measurement_unit_name", type="string", example="Unidad", nullable=false),
 *     @OA\Property(property="name", type="string", example="Laptop Gamer", nullable=false),
 *     @OA\Property(property="barcode", type="string", example="1234567890123", nullable=true),
 *     @OA\Property(property="barcode_path", type="string", example="/images/barcodes/1234567890123.png", nullable=true),
 *     @OA\Property(property="sale_price", type="number", format="float", example=1499.99, nullable=false),
 *     @OA\Property(property="purchase_price", type="number", format="float", example=1200.50, nullable=false),
 *     @OA\Property(property="minimum_stock", type="integer", example=5, nullable=false)
 * )
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand_id' => $this->brand_id,
            'category_id' => $this->category_id,
            'measurement_unit_id' => $this->measurement_unit_id,
            'measurement_unit_name' => $this->measurement_unit_name,
            'name' => $this->name,
            'barcode' => $this->barcode,
            //'internal_code' => $this->internal_code, 
            'barcode_path' => $this->barcode_path,
            'sale_price' => $this->sale_price,
            'purchase_price' => $this->purchase_price,
            'minimum_stock' => $this->minimum_stock,
        ];
        
    }
}
