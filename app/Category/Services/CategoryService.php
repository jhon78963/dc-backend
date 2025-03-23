<?php

namespace App\Category\Services;

use App\Category\Models\Category;
use App\Shared\Services\ModelService;

class CategoryService
{

    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newCategory): void
    {
        $this->modelService->create(new Category(), $newCategory);
    }

    public function delete(Category $Category): void
    {
        $this->modelService->delete($Category);
    }

    public function update(Category $Category, array $editCategory): void
    {
        $this->modelService->update($Category, $editCategory);
    }

    public function checkCategory( string $name): ?array
    {
        $nameExists = $this->CategoryExistsByName($name);
        
        return [$nameExists];
    }

    public function CategoryExistsByName(string $name): bool
    {
        return Category::where('name', $name)
            ->where('is_deleted', false)
            ->exists();
    }

    public function validate(Category $Category, string $modelName): Category
    {
        return $this->modelService->validate($Category, $modelName);
    }
}
