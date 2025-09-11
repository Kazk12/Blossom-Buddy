<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;


class PlantController extends Controller
{
    use HttpResponses;

    public function index(Request $request){

        $plants = Plant::all();
        return $this->success($plants);

    }

    public function store(Request $request){

        $validatedData = $request->validate([
            'common_name' => 'required|string|max:255',
            'watering_general_benchmark' => 'required|string|max:255',
        ]);
        $plant = Plant::create([
            'common_name' => $validatedData['common_name'],
            'watering_general_benchmark' => $validatedData['watering_general_benchmark'],
        ]);
        return $this->success($plant, 'Plant created successfully', 201);

    }

    public function show($name){

        $plant = Plant::where('common_name', $name)->first();
        if (!$plant) {
            return $this->error(null, 'Plant not found', 404);
        }
        return $this->success($plant);

    }

    public function destroy($id){

        $plant = Plant::find($id);
        if (!$plant) {
            return $this->error(null, 'Plant not found', 404);
        }
        $plant->delete();
        return $this->success(null, 'Plant deleted successfully');

    }
}