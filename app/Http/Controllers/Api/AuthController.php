<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Kredensial Invalid'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Gagal Buat Token: ' . $e->getMessage()], 500);
        }

        return response()->json(['token' => $token], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = User::create([
          'email' => $request->email,
          'password' => bcrypt($request->password),
        ]);

        try {
            $customer = Customer::create([
                'customer_no' => str_pad($user->id, 3, '0', STR_PAD_LEFT),
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->phone,
                'email' => $request->email,
            ]);

            return response()->json(['message' => 'Registrasi Berhasil, silakan login.'], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Registrasi Gagal: ' . $e->getMessage()], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json(['message' => 'Logout Berhasil'], 200);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Gagal Logout: ' . $e->getMessage()], 500);
        }
    }
}
