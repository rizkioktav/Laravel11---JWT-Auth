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
  public function index()
  {
    try {
        $sales = Sale::all();
        if ($sales->isEmpty()) {
            return response()->json(['message' => 'Data sale tidak ada'], 200);
        } else {
            return response()->json($sales, 200);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Gagal mengambil data penjualan: ' . $e->getMessage()], 500);
    }
  }
  public function store(Request $request)
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
          return response()->json(['error' => 'Customer invalid.'], 404);
      }
      //itung manual aja
      $subtotal = $request->qty * $request->price;
      $discount = ($request->discount / 100) * $subtotal;
      $tax = ($request->tax / 100) * ($subtotal - $discount);
      $total = $subtotal - $discount + $tax;
  
      try {
          $sale = Sale::create([
              'sale_no' => 'TEMP',
              'customer_no' => $customer->customer_no,
              'product' => $request->product,
              'qty' => $request->qty,
              'price' => $request->price,
              'discount' => $request->discount,
              'tax' => $request->tax,
              'total' => $total,
          ]);
          $sale->sale_no = 'S' . str_pad($sale->id, 4, '0', STR_PAD_LEFT);
          $sale->save();
  
          return response()->json(['message' => 'Penjualan berhasil dibuat', 'sale' => $sale], 201);
      } catch (\Exception $e) {
          return response()->json(['error' => 'Gagal menambahkan penjualan: ' . $e->getMessage()], 500);
      }
    }

    public function show($id)
    {
        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer invalid.'], 404);
        }

        $sale = Sale::where('id', $id)->where('customer_no', $customer->customer_no)->first();

        if (!$sale) {
            return response()->json(['error' => 'Penjualan tidak ditemukan atau tidak milik customer ini.'], 404);
        }
        try {
            return response()->json($sale, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mendapatkan penjualan: ' . $e->getMessage()], 500);
        }
    }

    public function showSalesByCustomer()
    {
        $user = JWTAuth::user();

        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer invalid.'], 404);
        }

        try {
            $sales = Sale::where('customer_no', $customer->customer_no)->get();
            if ($sales->isEmpty()) {
                return response()->json(['message' => 'Data sale tidak ada'], 200);
            }else {
                return response()->json($sales, 200);
            }
           
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengambil data penjualan: ' . $e->getMessage()], 500);
        }
    }


    public function update(Request $request, $id)
    {
        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer invalid.'], 404);
        }

        $sale = Sale::where('id', $id)->where('customer_no', $customer->customer_no)->first();

        if (!$sale) {
            return response()->json(['error' => 'Penjualan tidak ditemukan atau tidak milik customer ini.'], 404);
        }

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

        $subtotal = $request->qty * $request->price;
        $discount = ($request->discount / 100) * $subtotal;
        $tax = ($request->tax / 100) * ($subtotal - $discount);
        $total = $subtotal - $discount + $tax;

        try {
            $sale->update([
                'product' => $request->product,
                'qty' => $request->qty,
                'price' => $request->price,
                'discount' => $request->discount,
                'tax' => $request->tax,
                'total' => $total,
            ]);

            return response()->json(['message' => 'Penjualan berhasil diperbarui', 'sale' => $sale], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal memperbarui penjualan: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $user = JWTAuth::user();
        $customer = Customer::where('email', $user->email)->first();

        if (!$customer) {
            return response()->json(['error' => 'Customer invalid.'], 404);
        }

        $sale = Sale::where('id', $id)->where('customer_no', $customer->customer_no)->first();

        if (!$sale) {
            return response()->json(['error' => 'Penjualan tidak ditemukan atau tidak milik customer ini.'], 404);
        }

        try {
            $sale->delete();
            return response()->json(['message' => 'Penjualan berhasil dihapus'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus penjualan: ' . $e->getMessage()], 500);
        }
    }
}
