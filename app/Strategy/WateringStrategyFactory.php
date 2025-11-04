<?php

namespace App\Strategy;

use App\Models\Plant;
use Illuminate\Contracts\Container\Container;

/**
 * Fabrique qui retourne la stratégie de watering adaptée à une plante.
 * Elle lit un attribut (ex: watering_general_benchmark.type) pour choisir la stratégie.
 */
class WateringStrategyFactory
{
    protected $container;

    protected $map = [
        'cactus' => CactusWateringStrategy::class,
        'tropical' => TropicalWateringStrategy::class,
        'default' => DefaultWateringStrategy::class,
    ];

    /**
     * Le constructeur accepte optionnellement le container. En contexte de test
     * où l'injection peut échouer, on utilise `app()` comme fallback.
     */
    public function __construct(?Container $container = null)
    {
        $this->container = $container ?? app();
    }

    /**
     * Retourne une implémentation de WateringStrategyInterface pour la plante donnée.
     *
     * @param Plant $plant
     */
    public function getStrategyForPlant(Plant $plant)
    {
        $benchmark = $plant->watering_general_benchmark ?? [];
        $type = null;

        if (is_array($benchmark) && isset($benchmark['type'])) {
            $type = strtolower($benchmark['type']);
        } elseif (!empty($plant->watering)) {
            $type = strtolower($plant->watering);
        }

        $class = $this->map[$type] ?? $this->map['default'];

        return $this->container->make($class);
    }
}
