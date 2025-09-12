<?php 

namespace App\Interfaces;

interface PlantsServiceInterface 
{
    public function fetchAndStorePlantsData(): void;
}