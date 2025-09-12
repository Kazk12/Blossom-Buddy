<?php

namespace App\Console\Commands;

use App\Interfaces\PlantsServiceInterface;
use Illuminate\Console\Command;

class ParceApiPlant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:parce-api-plant';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch the first 50 plants from the API and store them in the database';


    protected PlantsServiceInterface $plantService;

    public function __construct(PlantsServiceInterface $plantService)
    {
        parent::__construct();

        $this->plantService = $plantService;
    }
    

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching plants...');
        $this->plantService->fetchAndStorePlantsData();
        $this->info('Plants fetched and stored successfully.');
    }
}
