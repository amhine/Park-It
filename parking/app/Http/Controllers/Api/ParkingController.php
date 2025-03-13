<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\searchparking;
use App\Http\Requests\storparking;
use App\Http\Requests\updateparking;
use Illuminate\Support\Facades\DB;
use App\Models\Parkings;
use App\Models\Places;

class ParkingController extends Controller
{
    public function searshParking(searchParking $request)
    {
        
        $validated = $request->validated();

        $address = $validated['adress']  ?? null ;
        $titre = $validated['titre']  ?? null;

        $query = DB::table('parkings');

        if ($address || $titre) {
            $query->where(function ($q) use ($address, $titre) {
                if ($address) {
                    $q->where('adress', 'ILIKE', "%{$address}%");
                }
                if ($titre) {
                    $q->where('titre', 'ILIKE', "%{$titre}%"); 
                }
            });
        }

        $parkings = $query->get();

        if ($parkings->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Aucun parking trouvé pour cette recherche',
                'data' => []
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Liste des parkings trouvés',
            'data' => $parkings
        ], 200);
    }

    public function store(storparking $request)
    {
        try {
            
        $validated = $request->validated();

            $parking = Parkings::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Parking ajouté avec succès',
                'data' => $parking
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later.'
            ], 500);
        }
    }

    // public function update(updateparking  $request, $id)
    // {
    //     try {
    //         $parking = Parkings::findOrFail($id);


    //         $parking->update($request->all());

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Parking mis à jour avec succès',
    //             'data' => $parking
    //         ], 200);
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Something went wrong, please try again later.'
    //         ], 500);
    //     }
    // }

    public function update(updateparking $request, $id)
{
    try {
        $parking = Parkings::findOrFail($id);
        $validatedData = $request->validated();

        $parking->update($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Parking mis à jour avec succès',
            'data' => $parking
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Une erreur est survenue, veuillez réessayer plus tard.',
            'error' => $th->getMessage()
        ], 500);
    }
}


    public function destroy(Request $request)
    {
        try {
            $id = $request->id;

            return response()->json([
                'success' => true,
                'message' => 'Parking supprimé avec succès'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later.'
            ], 500);
        }
    }
    public function initialiserPlaces($parkingId)
{
    try {
        $parking = Parkings::findOrFail($parkingId);
        $placesExistantes = Places::where('parking_id', $parkingId)->count();
        
        if ($placesExistantes > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Ce parking a déjà des places initialisées'
            ], 400);
        }
        $places = [];
        for ($i = 1; $i <= $parking->nombre_total_places; $i++) {
            $places[] = [
                'numero' => $i,
                'parking_id' => $parkingId,
                'est_disponible' => true,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }
        
        Places::insert($places);
        $parking->update(['places_disponibles' => $parking->nombre_total_places]);
        
        return response()->json([
            'success' => true,
            'message' => $parking->nombre_total_places . ' places ont été créées pour ce parking',
            'data' => Places::where('parking_id', $parkingId)->get()
        ], 201);
        
    } catch (\Throwable $th) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de l\'initialisation des places',
            'error' => $th->getMessage()
        ], 500);
    }
}
}