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
     *     path="/plants",
     *     summary="Search plants or get all plants",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         description="Search query for plant names",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('q')) {
            $results = app(\App\Services\PlantService::class)->searchPlantByName($request->q);
            return $this->success($results);
        }

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
     *     path="/plants/{name}",
     *     summary="Get complete plant data by name",
     *     tags={"Plants"},
     *     @OA\Response(response=200, description="Successful operation"),
     *     @OA\Response(response=404, description="Plant not found")
     * )
     */
    public function show($name)
    {
        $plantData = app(\App\Services\PlantService::class)->checkAndCompleteData($name);

        if (!$plantData) {
            return $this->error(null, 'Plant not found', 404);
        }

        return $this->success($plantData);
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