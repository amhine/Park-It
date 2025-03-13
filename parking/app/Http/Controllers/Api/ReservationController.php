<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservations;
use App\Models\Parkings;
use Illuminate\Support\Facades\Auth;
use App\Models\Places;
use Illuminate\Support\Facades\DB;

class ReservationController extends Controller
{
    public function reserverPlace(Request $request)
    {
        try {
            $validated = $request->validate([
                'heure_arrivee' => 'required|date_format:Y-m-d H:i:s',
                'heure_depart' => 'required|date_format:Y-m-d H:i:s|after:heure_arrivee',
                'parking_id' => 'required|exists:parkings,id',
            ]);

            $parking = Parkings::findOrFail($validated['parking_id']);
            if ($parking->places_disponibles <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune place disponible dans ce parking.'
                ], 400);
            }
            $place = Places::where('parking_id', $validated['parking_id'])
                            ->where('est_disponible', true)
                            ->first();

            if (!$place) {
                return response()->json([
                    'success' => false,
                    'message' => 'Toutes les places sont déjà réservées.'
                ], 400);
            }
            DB::beginTransaction();
            $reservation = Reservations::create([
                'heure_arrivee' => $validated['heure_arrivee'],
                'heure_depart' => $validated['heure_depart'],
                'user_id' => Auth::id(),
                'parking_id' => $validated['parking_id'],
                'place_id' => $place->id,
                'statut' => 'confirmée'
            ]);
            $place->update(['est_disponible' => false]);
            $parking->decrement('places_disponibles');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation effectuée avec succès.',
                'data' => $reservation
            ], 201);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la réservation.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
    public function annulerReservation($id)
    {
        try {
            $reservation = Reservations::findOrFail($id);

            // Début de la transaction pour garantir l'intégrité des données
            DB::beginTransaction();

            // Rendre la place à nouveau disponible
            $place = Places::findOrFail($reservation->place_id);
            $place->update(['est_disponible' => true]);

            // Incrémenter le nombre de places disponibles dans le parking
            $parking = Parkings::findOrFail($reservation->parking_id);
            $parking->increment('places_disponibles');

            // Supprimer la réservation
            $reservation->delete();

            // Commit de la transaction
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Réservation annulée avec succès.'
            ], 200);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'annulation de la réservation.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
