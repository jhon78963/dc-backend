<?php

namespace App\Product\Services;

use App\Product\Models\Product;
use App\Shared\Services\ModelService;

class ProductService
{

    protected ModelService $modelService;

    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    public function create(array $newProduct): void
    {
        $this->modelService->create(new Product(), $newProduct);
    }

    public function delete(User $user): void
    {
        $this->modelService->delete($user);
    }

    public function update(User $user, array $editUser): void
    {
        $this->modelService->update($user, $editUser);
    }

    public function checkUser(string $email, string $username): ?array
    {
        $emailExists = $this->userExistsByEmail($email);
        $usernameExists = $this->userExistsByUsername($username);
        return [$emailExists, $usernameExists];
    }

    public function userExistsByEmail(string $email): bool
    {
        return User::where('email', $email)
            ->where('is_deleted', false)
            ->exists();
    }

    public function userExistsByUsername(string $username): bool
    {
        return User::where('username', $username)
            ->where('is_deleted', false)
            ->exists();
    }

    public function validate(User $user, string $modelName): User
    {
        return $this->modelService->validate($user, $modelName);
    }
}
