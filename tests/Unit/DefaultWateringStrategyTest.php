<?php

use App\Models\Plant;
use App\Strategy\DefaultWateringStrategy;

it('returns 7 days by default for next watering', function () {
    $plant = new Plant(['watering_general_benchmark' => []]);
    $strategy = new DefaultWateringStrategy();

    $days = $strategy->calculateDaysUntilNextWatering($plant, [], true);

    expect($days)->toBeInt()->toBe(7);
});

it('returns 0 when needs_water is false', function () {
    $plant = new Plant(['watering_general_benchmark' => ['frequency_days' => 7]]);
    $strategy = new DefaultWateringStrategy();

    $days = $strategy->calculateDaysUntilNextWatering($plant, [], false);
    expect($days)->toBeInt()->toBe(0);
});
