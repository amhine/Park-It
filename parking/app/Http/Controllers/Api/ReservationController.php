<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parkings;
use App\Models\Reservations;
use App\Http\Requests\Reserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    
    public function reserver(Reserver $request)
    {
        try {
            $parking = Parkings::findOrFail($request->parking_id);

            if ($parking->places_disponibles <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune place disponible dans ce parking'
                ], 400);
            }

            $existingReservation = Reservations::where('parking_id', $request->parking_id)
                ->where(function($query) use ($request) {
                    $query->whereBetween('heure_arrivee', [$request->heure_arrivee, $request->heure_depart])
                        ->orWhereBetween('heure_depart', [$request->heure_arrivee, $request->heure_depart])
                        ->orWhere(function($query) use ($request) {
                            $query->where('heure_arrivee', '<=', $request->heure_arrivee)
                                  ->where('heure_depart', '>=', $request->heure_depart);
                        });
                })
                ->exists();

            if ($existingReservation) {
                return response()->json([
                    'success' => false,
                    'message' => 'La place de parking est déjà réservée pour cette période.'
                ], 400);
            }

            $reservation = Reservations::create([
                'user_id' => Auth::id(),
                'heure_arrivee' => $request->heure_arrivee,
                'heure_depart' => $request->heure_depart,
                'parking_id' => $request->parking_id,
                'statut' => 'en attente',
            ]);

            $parking->decrement('places_disponibles');

            return response()->json([
                'success' => true,
                'message' => 'Réservation effectuée avec succès',
                'data' => $reservation
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réservation',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}

