<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    public $timestamps = false;
    
    public $fillable = [
        'common_name',
        'watering_general_benchmark'
    ];

    public $casts = [
        'watering_general_benchmark' => 'array'
    ];

    public function users(){
        return $this->belongsToMany(
            User::class
        );
    }
}
