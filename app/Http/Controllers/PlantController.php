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

    }

    public function show($name){

    }

    public function destroy($id){

    }
}