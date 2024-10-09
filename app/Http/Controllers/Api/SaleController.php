<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class SaleController extends Controller
{
  public function sale(Request $request)
  {
      $user = JWTAuth::user();
  
      $validator = Validator::make($request->all(), [
          'product' => 'required|string',
          'qty' => 'required|numeric|min:1',
          'price' => 'required|numeric|min:0',
          'discount' => 'nullable|numeric|min:0|max:100',
          'tax' => 'nullable|numeric|min:0|max:100',
      ]);
  
      if ($validator->fails()) {
          return response()->json($validator->errors(), 422);
      }
  
      $customer = Customer::where('email', $user->email)->first();
      if (!$customer) {
          return response()->json(['error' => 'Customer not found for this user.'], 404);
      }
      //itung manual aja
      $subtotal = $request->qty * $request->price;
      $discount = ($request->discount / 100) * $subtotal;
      $tax = ($request->tax / 100) * ($subtotal - $discount);
      $total = $subtotal - $discount + $tax;
  
      try {
          $sale_no = 'S' . str_pad(Sale::count() + 1, 4, '0', STR_PAD_LEFT);
          $sale = Sale::create([
              'sale_no' => $sale_no,
              'customer_no' => $customer->customer_no,
              'product' => $request->product,
              'qty' => $request->qty,
              'price' => $request->price,
              'discount' => $request->discount,
              'tax' => $request->tax,
              'total' => $total,
          ]);
  
          return response()->json(['message' => 'Sale created successfully', 'sale' => $sale], 201);
      } catch (\Exception $e) {
          return response()->json(['error' => 'Gagal menambahkan penjualan: ' . $e->getMessage()], 500);
      }
  }
  
}
