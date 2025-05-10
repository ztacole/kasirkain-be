<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = Transaction::with('user', 'details.productVariant.product')->where('user_id', $request->user()->id)->withCount('details')->orderByDesc('id')->get();

        if ($request->has('user_id')) {
            $transactions = $transactions->where('user_id', $request->user_id);
        }

        if ($request->has('month')) {
            $transactions = $transactions->whereMonth('created_at', $request->month);
        }

        if ($request->has('year')) {
            $transactions = $transactions->whereYear('created_at', $request->year);
        }

        if ($request->has('payment_type')) {
            $transactions = $transactions->where('payment_type', $request->payment_type);
        }

        $response = $transactions->groupBy(function ($transaction) {
            return $transaction->created_at->format('Y-m-d');
        })->map(function ($group, $date) {
            $transactionGroupped =  $group->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'user' => [
                        'id' => $transaction->user->id,
                        'username' => $transaction->user->username
                    ],
                    'payment_type' => $transaction->payment_type,
                    'productCount' => $transaction->details->count(),
                    'total' => $transaction->details->sum(function ($details) {
                        return $details->productVariant->product->price * $details->quantity;
                    }),
                    'created_at' => $transaction->created_at
                ];
            });

            return [
                'date' => $date,
                'transactions' => $transactionGroupped
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'payment_type' => 'required|string|max:255',
                'details' => 'required|array',
                'details.*.product_variant_id' => 'required|exists:product_variants,id',
                'details.*.quantity' => 'required|integer|min:1',
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
            $transaction = Transaction::create([
                'user_id' => $request->user_id,
                'payment_type' => $request->payment_type
            ]);

            // Create detail transaksi and update stock
            foreach ($request->details as $detail) {
                // Check stock availability
                $variant = ProductVariant::findOrFail($detail['product_variant_id']);
                if ($variant->stock < $detail['quantity']) {
                    throw new \Exception("Insufficient stock for product variant ID: {$variant->id}");
                }

                // Create detail
                TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $detail['product_variant_id'],
                    'quantity' => $detail['quantity'],
                ]);

                // Update stock
                $variant->update([
                    'stock' => $variant->stock - $detail['quantity']
                ]);
            }

            DB::commit();

            $response = new TransactionResource($transaction->load('user', 'details.productVariant.product.category'));

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction has been completed successfully',
                'data' => $response,
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
        $transaction = Transaction::with('user', 'details.productVariant.product.category')
            ->find($id);

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Transaction not found',
            ], 404);
        }

        $response = new TransactionResource($transaction);

        return response()->json([
            'status' => 'success',
            'data' => $response,
        ]);
    }
}
