<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;

/**
 * Class UserRepository.
 */
class AuthRepository implements AuthRepositoryInterface
{
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    // public function getUser(int $id): ?User
    // {
    //     return User::find($id);
    // }

    

    // public function updateUser(int $id, array $data): ?User
    // {
    //     $user = User::find($id);
    //     if ($user) {
    //         $user->update($data);
    //         return $user;
    //     }
    //     return null;
    // }

    // public function deleteUser(int $id): bool
    // {
    //     $user = User::find($id);
    //     if ($user) {
    //         $user->delete();
    //         return true;
    //     }
    //     return false;
    // }

    // public function getAllUsers(): array
    // {
    //     return User::all()->toArray();
    // }
}
