<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    public $timestamps = false;

    public $fillable = [
        'common_name',
        'watering_general_benchmark',
        'api_id',
        'watering',
        'watering_period',
        'flowers',
        'fruits',
        'leaf',
        'growth_rate',
        'maintenance'
    ];

    public $casts = [
        'watering_general_benchmark' => 'array'
    ];

    public function users(){
        return $this->belongsToMany(
            User::class, 'user_plant'
        );
    }
}
