<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        try {
            $customers = Customer::all();
            return response()->json($customers, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data customer: ' . $e->getMessage()], 500);
        }
    }

    public function show()
    {
        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();
        if (!$customer) {
          return response()->json(['error' => 'Customer tidak ditemukan'], 404);
        }
        try {
            return response()->json($customer, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data customer: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        DB::beginTransaction();

        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->json()->all(), [
            'name' => 'required|string',
            'address' => 'required|string',
            'phone' => 'required|string|max:14',
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $newName = $request->json('name');
            $newAddress = $request->json('address');
            $newPhone = $request->json('phone');
            $newEmail = $request->json('email');

            $user->email = $newEmail;
            $user->save();

            $customer->name = $newName;
            $customer->address = $newAddress;
            $customer->phone = $newPhone;
            $customer->email = $newEmail;
            $customer->save();

            DB::commit();

            return response()->json(['message' => 'Customer berhasil diperbarui', 'customer' => $customer], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal memperbarui customer: ' . $e->getMessage()], 500);
        }
    }

    public function destroy()
    {
        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();

        try {
            if (!$customer) {
                return response()->json(['error' => 'Customer tidak ditemukan'], 404);
            }
            $customer->sales()->delete();
            $customer->delete();
            return response()->json(['message' => 'Customer berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus customer: ' . $e->getMessage()], 500);
        }
    }
}
