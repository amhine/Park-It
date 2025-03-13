<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Parkings;
use App\Models\Reservation;
use App\Http\Requests\Reserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    
    // public function reserver(Reserver $request)
    // {
    //     try {
    //         // dd(Auth::id());
    //         // // Vérifier si l'utilisateur est authentifié
    //         // if (!Auth::check()) {
    //         //     return response()->json([
    //         //         'success' => false,
    //         //         'message' => 'Utilisateur non authentifié'
    //         //     ], 401);
    //         // }

    //         $parking = Parkings::findOrFail($request->parking_id);

    //         if ($parking->places_disponibles <= 0) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Aucune place disponible dans ce parking'
    //             ], 400);
    //         }

    //         // Créer la réservation
    //         $reservation = Reservation::create([
    //             'user_id' => Auth::id(),
    //             'heure_arrivee' => $request->heure_arrivee,
    //             'heure_depart' => $request->heure_depart,
    //             'parking_id' => $request->parking_id,
    //             'statut' => 'en attente',
    //         ]);

    //         // Mettre à jour les places disponibles
    //         $parking->decrement('places_disponibles');

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Réservation effectuée avec succès',
    //             'data' => $reservation
    //         ], 201);
            
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de la réservation',
    //             'error' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    public function reserver(Reserver $request)
{
    try {
        // L'utilisateur est déjà authentifié grâce au middleware 'auth:sanctum'
        // pas besoin de vérifier à nouveau avec Auth::check()

        $parking = Parkings::findOrFail($request->parking_id);

        if ($parking->places_disponibles <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune place disponible dans ce parking'
            ], 400);
        }

        // Créer la réservation
        $reservation = Reservation::create([
            'user_id' => Auth::id(),  
            'heure_arrivee' => $request->heure_arrivee,
            'heure_depart' => $request->heure_depart,
            'parking_id' => $request->parking_id,
            'statut' => 'en attente',
        ]);

        // Mettre à jour les places disponibles
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
