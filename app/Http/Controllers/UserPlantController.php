<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use App\Interfaces\WeatherServiceInterface;
use App\Strategy\WateringStrategyFactory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;
use Carbon\Carbon;



class UserPlantController extends Controller
{
    use HttpResponses;

    protected $weatherService;
    protected $wateringStrategyFactory;

    /**
     * Injecte le service météo et la fabrique de stratégies d'arrosage.
     */
    public function __construct(WeatherServiceInterface $weatherService, WateringStrategyFactory $wateringStrategyFactory)
    {
        $this->weatherService = $weatherService;
        $this->wateringStrategyFactory = $wateringStrategyFactory;
    }

        /**
     * @OA\GET(
     *     path="/user/plants",
     *     summary="Get a list of plants for the authenticated user",
     *     parameters={},
     *     tags={"User Plants"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function index(Request $request){

        $user = $request->user();
        $plants = $user->plants;

        return $this->success($plants);

    }

    /**
     * @OA\POST(
     *     path="/user/plant",
     *     summary="Add a plant to user's collection with optional city",
     *    parameters={
     *         @OA\Parameter(name="common_name", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(
     *             name="city",
     *             in="query",
     *             required=false,
     *             @OA\Schema(
     *                 type="string",
     *                 enum={"Paris", "Lyon", "Marseille", "Bordeaux", "Lille", "Toulouse", "Nice", "Nantes", "Strasbourg", "Montpellier", "Rennes"}
     *             ),
     *             description="The city where the plant is located"
     *         ),
     *     },
     *     tags={"User Plants"},
     *     @OA\Response(response=201, description="Plant added successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

       public function store(Request $request){
        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'city' => ['nullable', 'string', Rule::in(config('cities.available'))]
        ]);

        $user = $request->user();
        
        // Recherche de la plante dans la base de données
        $plant = Plant::where('common_name', $request->common_name)->first();
        
        if (!$plant) {
            return $this->error(null, 'Plant not found in database', 404);
        }

        // Vérifie si l'utilisateur a déjà cette plante
        if ($user->plants()->where('plant_id', $plant->id)->exists()) {
            return $this->error(null, 'Plant already in your collection', 400);
        }

        // Attache la plante à l'utilisateur avec la ville
        $user->plants()->attach($plant->id, [
            'city' => $request->city,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Si une ville est spécifiée, récupérer les recommandations météo
        $weatherInfo = null;
        $needsWater = true; // Par défaut, on suggère d'arroser
        $currentWeather = null;

        if ($request->city) {
            $weatherInfo = $this->weatherService->getWeatherForCity($request->city);
            if (is_array($weatherInfo) && isset($weatherInfo['needs_water'])) {
                $needsWater = $weatherInfo['needs_water'];
                $currentWeather = $weatherInfo['current_weather'] ?? null;
            }
        }

        // Choisir la stratégie adaptée à la plante
        $strategy = $this->wateringStrategyFactory->getStrategyForPlant($plant);

        $daysUntilNextWatering = $strategy->calculateDaysUntilNextWatering(
            $plant,
            $currentWeather ?? [],
            $needsWater
        );

        // Calculer une date exacte pour le prochain arrosage (format chaîne lisible)
    // Calculer la date exacte du prochain arrosage
    $nextWateringCarbon = now()->addDays($daysUntilNextWatering);

    // Utiliser ISO8601 pour machine-readability
    $nextWateringAt = $nextWateringCarbon->toIso8601String();

    // Version lisible pour l'utilisateur (français) et relative
    // ex: "jeudi 6 novembre 2025 à 09:00" et "dans 3 jours"
    $nextWateringHuman = $nextWateringCarbon->locale('fr')->isoFormat('dddd D MMMM YYYY [à] HH:mm');
    $nextWateringRelative = $nextWateringCarbon->locale('fr')->diffForHumans();

        $response = [
            'plant' => $plant,
            'needs_water' => $needsWater,
            'weather' => $currentWeather,
            // Date ISO8601 (machine) et deux formats lisibles en français
            'next_watering_at' => $nextWateringAt,
            'next_watering_human' => $nextWateringHuman,
            'next_watering_relative' => $nextWateringRelative,
        ];

        return $this->success($response, "Plant successfully added to user's collection", 201);
    }

    /**
     * @OA\DELETE(
     *     path="/user/plant/{id}",
     *     summary="Delete a plant for the authenticated user",
     *    parameters={
     *         @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     },
     *     tags={"User Plants"},
     *     @OA\Response(response=200, description="Plant deleted successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

    public function destroy($id, Request $request){
        $user = $request->user();
        $plant = $user->plants()->find($id);
        
        if (!$plant) {
            return $this->error(null, 'Plant not found in your collection', 404);
        }

        // On détache uniquement la plante de l'utilisateur sans la supprimer de la base de données
        $user->plants()->detach($plant->id);
        
        return $this->success(null, 'Plant successfully removed from your collection', 200);

    }
}