<?php

namespace App\Warehouse\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/**
 * @OA\Schema(
 *     schema="WarehouseResource",
 *     type="object",
 *     title="Warehouse Resource",
 *     description="Recurso que representa un almacén. Nota: Solo puede existir un almacén de tipo PRINCIPAL donde is_deleted sea false.",
 *     required={"id", "name", "location", "type", "is_deleted"},
 *     
 *     @OA\Property(property="id", type="integer", example=1, description="ID único del almacén"),
 *     @OA\Property(property="name", type="string", example="ALMACÉN CENTRAL", description="Nombre del almacén"),
 *     @OA\Property(property="location", type="string", example="AV. PERÚ 123", description="Ubicación del almacén"),
 *     @OA\Property(property="type", type="string", enum={"PRINCIPAL", "SECUNDARIO"}, example="PRINCIPAL", description="Tipo de almacén"),
 *     
 *     @OA\Property(
 *         property="restrictions",
 *         type="object",
 *         description="Restricciones para almacenes de tipo PRINCIPAL",
 *         @OA\Property(property="max_principal_active", type="integer", example=1, description="Solo puede haber un almacén de tipo PRINCIPAL activo (is_deleted = false).")
 *     )
 * )
 */
class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        =>  $this->id,
            'name'      =>  $this->name,
            'location'  =>  $this->location,
            'type'      =>  $this->type
        ];
    }
}
