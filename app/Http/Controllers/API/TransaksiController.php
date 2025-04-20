<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransaksiResource;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use App\Models\VarianProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::with('user', 'detail.varianProduk.produk')->get();
        $response = $transaksi->map(function ($transaksi) {
            return [
                'id' => $transaksi->id,
                'user' => $transaksi->user,
                'jenis_pembayaran' => $transaksi->jenis_pembayaran,
                'total' => $transaksi->detail->sum(function ($detail) {
                    return $detail->varianProduk->produk->harga * $detail->jumlah;
                }),
                'created_at' => $transaksi->created_at,
                'updated_at' => $transaksi->updated_at
            ];
        });
        
        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'id_user' => 'required|exists:users,id',
                'jenis_pembayaran' => 'required|in:cash,transfer',
                'details' => 'required|array',
                'details.*.id_varian_produk' => 'required|exists:varian_produk,id',
                'details.*.jumlah' => 'required|integer|min:1',
            ]); 
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->validator->errors()->first(),
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Create transaksi
            $transaksi = Transaksi::create([
                'id_user' => $request->id_user,
                'jenis_pembayaran' => $request->jenis_pembayaran
            ]);

            // Create detail transaksi and update stock
            foreach ($request->details as $detail) {
                // Check stock availability
                $varian = VarianProduk::findOrFail($detail['id_varian_produk']);
                if ($varian->stok < $detail['jumlah']) {
                    throw new \Exception("Insufficient stock for product variant ID: {$varian->id}");
                }

                // Create detail
                DetailTransaksi::create([
                    'id_transaksi' => $transaksi->id,
                    'id_varian_produk' => $detail['id_varian_produk'],
                    'jumlah' => $detail['jumlah'],
                ]);

                // Update stock
                $varian->update([
                    'stok' => $varian->stok - $detail['jumlah']
                ]);
            }

            DB::commit();

            $data = $transaksi->load('user', 'detail.varianProduk.produk.kategori');

            return response()->json([
                'status' => 'success',
                'message' => 'Transaksi created successfully',
                'data' => $data,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        $transaksi = Transaksi::with('user', 'detail.varianProduk.produk.kategori')
            ->find($id);

        if (!$transaksi) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaksi not found',
            ], 404);
        }

        $data = new TransaksiResource($transaksi);
        
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}