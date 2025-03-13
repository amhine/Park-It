<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class parkings extends Model
{
    use HasFactory;
    protected $table = 'parkings';

    protected $fillable = [
        'titre',
        'adress',
        'nombre_total_places',
        'places_disponibles'
    ];
}
