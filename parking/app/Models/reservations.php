<?php



namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reservations extends Model
{
    use HasFactory;

    protected $table = 'reservations'; 
    protected $fillable = [
        'heure_arrivee',
        'heure_depart',
        'user_id',
        'parking_id',
        'statut'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parking()
    {
        return $this->belongsTo(Parkings::class, 'parking_id');
    }
}
