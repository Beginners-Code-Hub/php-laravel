<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        return view('pos.index');
    }

    public function lookup(string $barcode)
    {
        $medicine = Medicine::where('barcode', $barcode)->first();

        if (! $medicine) {
            return response()->json(['error' => 'Barcode not found.'], 404);
        }
        if ($medicine->expiry_date->isPast()) {
            return response()->json([
                'error' => "{$medicine->name} is EXPIRED. Do not sell.",
            ], 422);
        }
        if ($medicine->stock < 1) {
            return response()->json(['error' => "{$medicine->name} is out of stock."], 422);
        }

        return response()->json([
            'id' => $medicine->id,
            'name' => $medicine->name,
            'price' => (float) $medicine->price,
            'stock' => $medicine->stock,
            'expiry' => $medicine->expiry_date->format('M Y'),
        ]);
    }

    public function checkout(Request $request)
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:medicines,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'paid' => ['required', 'numeric', 'min:0'],
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $total = 0;
            $lines = [];

            foreach ($data['items'] as $item) {
                $medicine = Medicine::lockForUpdate()->findOrFail($item['id']);

                abort_if($medicine->expiry_date->isPast(), 422, "{$medicine->name} is expired.");
                abort_if($medicine->stock < $item['qty'], 422, "Not enough {$medicine->name}.");

                $medicine->decrement('stock', $item['qty']);
                $total += $medicine->price * $item['qty'];
                $lines[] = [
                    'medicine_id' => $medicine->id,
                    'quantity' => $item['qty'],
                    'price' => $medicine->price,
                ];
            }

            abort_if($data['paid'] < $total, 422, 'Payment is less than the total.');

            $sale = Sale::create([
                'user_id' => $request->user()->id,
                'receipt_no' => 'RX-' . now()->format('ymd-His') . '-' . random_int(10, 99),
                'total' => $total,
                'paid' => $data['paid'],
            ]);
            $sale->items()->createMany($lines);

            return $sale;
        });

        return response()->json(['receipt' => route('sales.show', $sale)]);
    }
}
