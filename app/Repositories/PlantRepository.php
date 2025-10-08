<?php

namespace App\Repositories;

use App\Interfaces\PlantRepositoryInterface;
use App\Models\Plant;

/**
 * Class PlantRepository.
 */
class PlantRepository implements PlantRepositoryInterface
{
    public function createPlant(array $data): Plant
    {
        return Plant::create($data);
    }

    public function findByPlant(string $name): ?Plant
    {
        return Plant::where('common_name', $name)->first();
    }

    public function delete(int $id): ?Plant
    {
        $plant = Plant::find($id);
        if ($plant) {
            $plant->delete();
            return $plant;
        }
        return null;
    }

    public function searchByCommonName(string $query, int $limit = 10): array
    {
        return Plant::where('common_name', 'LIKE', '%' . $query . '%')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function upsertByApiId(array $data): Plant
    {
        return Plant::updateOrCreate([
            'api_id' => $data['api_id'] ?? null
        ], $data);
    }

}