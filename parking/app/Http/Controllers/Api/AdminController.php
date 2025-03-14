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

   
