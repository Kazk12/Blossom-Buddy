<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;



class UserPlantController extends Controller
{
    use HttpResponses;

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

        // Attache la plante à l'utilisateur avec la ville (many-to-many avec données pivot)
        $user->plants()->attach($plant->id, [
            'city' => $request->city,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        

        return $this->success($plant, "Plant successfully added to user's collection", 201);
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