<?php

use App\Models\Plant;
use App\Strategy\CactusWateringStrategy;

it('reduces interval for small pot and sandy soil', function () {
    $strategy = new CactusWateringStrategy();

    $plant = new Plant(['watering_general_benchmark' => [
        'frequency_days' => 21,
        'pot_size' => 'small',
        'soil_type' => 'sandy'
    ]]);

    $days = $strategy->calculateDaysUntilNextWatering($plant, ['temperature' => 30, 'humidity' => 30], true);

    expect($days)->toBeInt()->toBeLessThan(21);
});
