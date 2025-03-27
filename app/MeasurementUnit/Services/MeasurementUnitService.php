<?php

namespace App\MeasurementUnit\Services;

use App\Brand\Models\Brand;
use App\Shared\Services\ModelService;

class MeasurementUnitService
{

    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newBrand): void
    {
        $this->modelService->create(new Brand(), $newBrand);
    }

    public function delete(Brand $brand): void
    {
        $this->modelService->delete($brand);
    }

    public function update(Brand $brand, array $editBrand): void
    {
        $this->modelService->update($brand, $editBrand);
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

    public function validate(Brand $brand, string $modelName): Brand
    {
        return $this->modelService->validate($brand, $modelName);
    }
}
