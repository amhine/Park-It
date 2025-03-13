<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\creatuser;
use App\Http\Requests\login;
class LoginController extends Controller
{
    /**
     * Create User
     * @param Request $request
     * @return User 
     */
  
    public function createUser(creatuser  $request)
    {
        try {
            $validateUser = $request->validated();
    
            $user = User::create([
                'name' => $validateUser['name'],
                'email' => $validateUser['email'],
                'password' => Hash::make($validateUser['password']),
                'idrole' => $validateUser['idrole'],
            ]);
    
          
    
            return response()->json([
                'status' => true,
                'message' => 'User Created Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 201);
    
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later.'
            ], 500);
        }
    }
    
    /**
     * Login The User
     * @param Request $request
     * @return User
     */
    public function loginUser(login $request)
    {
        try {
            if (!Auth::attempt($request->only(['email', 'password']))) {
                
                return response()->json([
                    'status' => false,
                    'message' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}