<?php

namespace App\Warehouse\Services;

use App\Shared\Services\ModelService;
use App\Warehouse\Models\Warehouse;

class WarehouseService
{

    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newWarehouse): void
    {
        $this->modelService->create(new Warehouse(), $newWarehouse);
    }

    public function delete(Warehouse $warehouse): void
    {
        $this->modelService->delete($warehouse);
    }

    public function update(Warehouse $warehouse, array $editWarehouse): void
    {
        $this->modelService->update($warehouse, $editWarehouse);
    }

    public function checkBrand( string $name): ?array
    {
        $nameExists = $this->brandExistsByName($name);
        
        return [$nameExists];
    }

    public function brandExistsByName(string $name): bool
    {
        return Brand::where('name', $name)
            ->where('is_deleted', false)
            ->exists();
    }

    public function validate(Warehouse $warehouse, string $modelName): Warehouse
    {
        return $this->modelService->validate($warehouse, $modelName);
    }
}
