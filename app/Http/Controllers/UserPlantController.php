<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
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
     *     summary="Create a new plant for the authenticated user",
     *    parameters={
     *         @OA\Parameter(name="common_name", in="query", required=true, @OA\Schema(type="string")),
     *         @OA\Parameter(name="watering_general_benchmark", in="query", required=true, @OA\Schema(type="array", @OA\Items(type="string"))),
     *     },
     *     tags={"User Plants"},
     *     @OA\Response(response=201, description="Plant created successfully"),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

       public function store(Request $request){

        $validated = $request->validate([
            'common_name' => 'required|string|max:255',
            'watering_general_benchmark' => 'required|array',
        ]);

        $user = $request->user();

        $plant = Plant::create([
            'common_name' => $request->common_name,
            'watering_general_benchmark' => json_encode($request->watering_general_benchmark),
        ]);

        // Attach the plant to the user (many-to-many)
        $user->plants()->attach($plant->id);

        return $this->success($plant, "Plant succesfully created by user " . $user->name, 201);
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
            return $this->error(null, 'Plant not found', 404);
        }
        // Detach the plant from the user before deleting
        $user->plants()->detach($plant->id);
        $plant->delete();
        return $this->success(null, 'Plant deleted successfully', 201);

    }
}