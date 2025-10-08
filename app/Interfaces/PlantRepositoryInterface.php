<?php 

namespace App\Interfaces;

use App\Models\Plant;

interface PlantRepositoryInterface
{
    public function createPlant(array $data): Plant;
    public function findByPlant(string $name): ?Plant;
    public function delete(int $id): ?Plant;

    // New methods to support service responsibilities
    public function searchByCommonName(string $query, int $limit = 10): array;
    public function upsertByApiId(array $data): Plant;
}