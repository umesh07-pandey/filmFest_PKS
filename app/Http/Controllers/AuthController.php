<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\AuthModel;
// use Illuminate\Support\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    //
    public function register(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                "name" => "required|string|max:255",
                "email" => "required|string|max:255|unique:AuthAdmin,email",
                "password" => "required|string|min:4",
                
            ]);

            $registration = AuthModel::create([
                "name" => $request->name,
                "email" => $request->email,
                "password" => Hash::make($request->password),

            ]);
            Log::info("data",["data"=>$registration]);

            DB::commit();

            return response()->json([
                "message" => "registration successfully",
                "data" => $registration,
                "status" => "true"
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "status" => "false"
            ], 500);
        }

    }

    public function login(Request $request)
    {
        try {
            DB::beginTransaction();

            $credentials = $request->only("email", "password");

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Invalid Credentials']);
            }
            DB::commit();
            return response()->json([
                "message" => "login successfully",
                "data" => $token,
                "status" => "true"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "message" => $e->getMessage(),
                "status" => "false"
            ], 500);
        }

    }

    public function logout(Request $request)
    {
        try {
            DB::beginTransaction();
            JWTAuth::invalidate(JWTAuth::getToken());
            DB::commit();
            return response()->json([
                "message" => "Logout successfully",
                "status" => "true"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error"=>$e->getMessage(),
                "status"=>"false"
            ],500);

        }

    }

}
