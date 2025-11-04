<?php

use App\Models\Plant;
use App\Strategy\TropicalWateringStrategy;

it('increases interval when humidity is high', function () {
    $strategy = new TropicalWateringStrategy();

    $plant = new Plant(['watering_general_benchmark' => [
        'frequency_days' => 3,
        'pot_size' => 'medium',
        'soil_type' => 'peat'
    ]]);

    $days = $strategy->calculateDaysUntilNextWatering($plant, ['temperature' => 25, 'humidity' => 80], true);

    expect($days)->toBeInt()->toBeGreaterThanOrEqual(3);
});
