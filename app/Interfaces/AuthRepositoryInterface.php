<?php 

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function createUser(array $data): User;
    // public function getUser(int $id): ?User;
    // public function updateUser(int $id, array $data): ?User;
    // public function deleteUser(int $id): bool;
    // public function getAllUsers(): array;
}