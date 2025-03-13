<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Places extends Model
{
    use HasFactory;
    
    protected $table = 'places';
    
    protected $fillable = [
        'numero',
        'parking_id',
        'est_disponible',
    ];
    
    public function parking()
    {
        return $this->belongsTo(Parkings::class, 'parking_id');
    }
    
    public function reservations()
    {
        return $this->hasMany(Reservations::class);
    }
}