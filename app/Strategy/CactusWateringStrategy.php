<?php

namespace App\Strategy;

use App\Interfaces\WateringStrategyInterface;
use App\Models\Plant;

/**
 * Stratégie pour les plantes de type Cactus / succulentes.
 * Logique : arrosages peu fréquents, être conservateur (retarder si sol sablonneux ou grande capacité)
 */
class CactusWateringStrategy implements WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(Plant $plant, array $weather = [], bool $needsWater = true): int
    {
        // Si la météo indique qu'il n'est pas nécessaire d'arroser
        if (!$needsWater) {
            return 0;
        }

        $benchmark = $plant->watering_general_benchmark ?? [];
        $baseDays = 21; // valeur par défaut pour cactus

        if (is_array($benchmark)) {
            if (isset($benchmark['frequency_days']) && is_numeric($benchmark['frequency_days'])) {
                $baseDays = (int) $benchmark['frequency_days'];
            }
        }

        // Ajustements selon la taille du pot (si fournie)
        $potSize = $benchmark['pot_size'] ?? null; // ex: small, medium, large
        if ($potSize === 'small') {
            $baseDays = max(7, $baseDays - 7);
        } elseif ($potSize === 'large') {
            $baseDays += 7;
        }

        // Sol sableux conserve moins l'eau -> arroser un peu plus souvent
        $soil = strtolower($benchmark['soil_type'] ?? '');
        if (str_contains($soil, 'sandy') || str_contains($soil, 'sable')) {
            $baseDays = max(7, $baseDays - 5);
        }

        // Conditions météo chaudes et sèches -> réduire intervalle
        $temp = $weather['temperature'] ?? null;
        $humidity = $weather['humidity'] ?? null;

        if (is_numeric($temp) && is_numeric($humidity)) {
            if ($temp > 30 && $humidity < 40) {
                $baseDays = max(3, $baseDays - 5);
            }
        }

        return (int) max(0, $baseDays);
    }
}
