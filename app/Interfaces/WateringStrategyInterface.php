<?php

namespace App\Interfaces;

interface WateringStrategyInterface
{
    public function calculateDaysUntilNextWatering(array $plantData): int;
}