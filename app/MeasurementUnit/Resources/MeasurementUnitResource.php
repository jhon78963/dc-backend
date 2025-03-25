<?php

namespace App\MeasurementUnit\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="MeasurementUnitResource",
 *     title="Measurement Unit Resource",
 *     description="Estructura de datos de una unidad de medida",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Kilogramo"),
 *     @OA\Property(property="symbol", type="string", example="kg")
 * )
 */
class MeasurementUnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'symbol'    => $this->symbol
        ];
    }
}
