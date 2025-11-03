<?php

use App\Strategy\DefaultWateringStrategy;

it('returns 7 days by default for next watering', function () {
    $strategy = new DefaultWateringStrategy();

    $days = $strategy->calculateDaysUntilNextWatering([
        'plant' => [],
        'weather' => [],
        'needs_water' => true,
    ]);

    expect($days)->toBeInt()->toBe(7);
});
