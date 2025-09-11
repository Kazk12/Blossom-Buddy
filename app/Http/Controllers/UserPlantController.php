<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;


class UserPlantController extends Controller
{
    use HttpResponses;

    public function index(Request $request){

        $user = $request->user();
        $plants = $user->plants;

        return $this->success($plants);

    }

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