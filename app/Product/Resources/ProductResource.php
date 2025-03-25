<?php

namespace App\Product\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
