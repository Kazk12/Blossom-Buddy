<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;



class PlantController extends Controller
{
    use HttpResponses;

    /**
 * @OA\Get(
 *     path="/plant",
 *     summary="Get a list of plants",
 *     tags={"Plants"},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=400, description="Invalid request")
 * )
 */

    public function index(Request $request){

        $plants = Plant::all();
        return $this->success($plants);

    }

    /**
 * @OA\Post(
 *     path="/plant",
 *     summary="Create a new plant",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"common_name","watering_general_benchmark"},
 *             @OA\Property(property="common_name", type="string"),
 *             @OA\Property(property="watering_general_benchmark", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     tags={"Plants"},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=400, description="Invalid request")
 * )
 */

    public function store(Request $request){

        $validatedData = $request->validate([
            'common_name' => 'required|string|max:255',
            'watering_general_benchmark' => 'required|array|max:255',
        ]);
        $plant = Plant::create([
            'common_name' => $validatedData['common_name'],
            'watering_general_benchmark' => $validatedData['watering_general_benchmark'],
        ]);
        return $this->success($plant, 'Plant created successfully', 201);

    }

       /**
 * @OA\Get(
 *     path="/plant/{name}",
 *     summary="Get a specific plant by name",
 *     parameters={
 *         @OA\Parameter(name="name", in="path", required=true, @OA\Schema(type="string")),
 *     },
 *     tags={"Plants"},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=404, description="Plant not found")
 * )
 */

    public function show($name){

        $plant = Plant::where('common_name', 'LIKE', "%{$name}%")->first();
        if (!$plant) {
            return $this->error(null, 'Plant not found', 404);
        }
        return $this->success($plant);

    }

         /**
 * @OA\Delete(
 *     path="/plant/{id}",
 *     summary="Delete a specific plant",
 *     parameters={
 *         @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
 *     },
 *     tags={"Plants"},
 *     @OA\Response(response=200, description="Successful operation"),
 *     @OA\Response(response=404, description="Plant not found")
 * )
 */

    public function destroy($id){

        $plant = Plant::find($id);
        if (!$plant) {
            return $this->error(null, 'Plant not found', 404);
        }
        $plant->delete();
        return $this->success(null, 'Plant deleted successfully');

    }
}