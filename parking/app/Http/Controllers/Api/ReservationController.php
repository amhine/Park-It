<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reservations;
use App\Models\Parkings;
use Illuminate\Support\Facades\Auth;
use App\Models\Places;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\reserveplace;

class ReservationController extends Controller
{
    public function reserverPlace(reserveplace $request)
    {
        try {

            $validated = $request->validated();

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

            DB::beginTransaction();

            $place = Places::findOrFail($reservation->place_id);
            $place->update(['est_disponible' => true]);
    
            $parking = Parkings::findOrFail($reservation->parking_id);
            $parking->increment('places_disponibles');
            $reservation->delete();
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
    public function afficherReservationsUtilisateur()
    {
        try {
            $reservations = Reservations::with(['user', 'parking', 'place'])->where('user_id', Auth::id()) ->get();

            if ($reservations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune réservation trouvée pour cet utilisateur.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Liste des réservations de l\'utilisateur.',
                'data' => $reservations
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'affichage des réservations.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function modifierReservation($id, reserveplace $request)
{
    try {
        $validated = $request->validated();
        $reservation = Reservations::findOrFail($id);

        if ($validated['heure_depart'] <= $validated['heure_arrivee']) {
            return response()->json([
                'success' => false,
                'message' => 'L\'heure de départ doit être après l\'heure d\'arrivée.'
            ], 400);
        }
        $parking = Parkings::findOrFail($validated['parking_id']);
        if ($parking->places_disponibles <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Aucune place disponible dans ce parking.'
            ], 400);
        }

        $place = Places::where('parking_id', $validated['parking_id'])->where('est_disponible', true)->first();

        if (!$place) {
            return response()->json([
                'success' => false,
                'message' => 'Toutes les places sont déjà réservées.'
            ], 400);
        }
        DB::beginTransaction();
        $reservation->update([
            'heure_arrivee' => $validated['heure_arrivee'],
            'heure_depart' => $validated['heure_depart'],
            'user_id' => Auth::id(),
            'parking_id' => $validated['parking_id'],
            'place_id' => $place->id,
            'statut' => 'confirmée',
        ]);
        $place->update(['est_disponible' => false]);
        $parking->decrement('places_disponibles');
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Réservation modifiée avec succès.',
            'data' => $reservation
        ], 200);

    } catch (\Throwable $th) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la modification de la réservation.',
            'error' => $th->getMessage()
        ], 500);
    }
}


}
