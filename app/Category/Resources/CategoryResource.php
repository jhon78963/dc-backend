<?php

namespace App\Category\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="CategoryResource",
 *     title="Category Resource",
 *     description="Estructura de datos de una categoría",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1, description="ID único de la categoría", nullable=false),
 *     @OA\Property(property="name", type="string", example="Electrónica", description="Nombre de la categoría", nullable=false)
 * )
 */
class CategoryResource extends JsonResource
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
            'name'      => $this->name
        ];
    }
}
