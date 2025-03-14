<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\storparking;
use App\Http\Requests\updateparking;
use App\Models\Parkings;

class AdminController extends Controller
{
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
    public function statistique()
{
    try {
        $totalParkings = Parkings::count();
        $totalPlaces = Parkings::sum('nombre_total_places');
        $availablePlaces = Parkings::sum('places_disponibles');

        return response()->json([
            'total_parkings' => $totalParkings,
            'total_places' => $totalPlaces,
            'places_disponibles' => $availablePlaces
        ], 200);
    } catch (\Throwable $th) {
        return response()->json([
            'status' => false,
            'message' => 'Erreur lors de la récupération des statistiques.',
        ], 500);
    }
}

}
