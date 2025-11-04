<?php

use App\Models\Plant;
use App\Strategy\WateringStrategyFactory;
use App\Strategy\CactusWateringStrategy;
use App\Strategy\DefaultWateringStrategy;
use App\Strategy\TropicalWateringStrategy;

it('choisit la strategie cactus si le type est cactus', function () {
    // Le container de test fournit la fonction app() via TestCase de Pest
    $factory = app(WateringStrategyFactory::class);

    $plant = new Plant(['watering_general_benchmark' => ['type' => 'cactus']]);

    $strategy = $factory->getStrategyForPlant($plant);

    expect($strategy)->toBeInstanceOf(CactusWateringStrategy::class);
});

it('retourne la strategie par defaut si aucun type connu', function () {
    $factory = app(WateringStrategyFactory::class);
    $plant = new Plant(['watering_general_benchmark' => []]);

    $strategy = $factory->getStrategyForPlant($plant);

    expect($strategy)->toBeInstanceOf(DefaultWateringStrategy::class);
});

it('choisit la strategie tropical si plant->watering indique tropical', function () {
    $factory = app(WateringStrategyFactory::class);
    $plant = new Plant(['watering' => 'tropical']);

    $strategy = $factory->getStrategyForPlant($plant);

    expect($strategy)->toBeInstanceOf(TropicalWateringStrategy::class);
});
