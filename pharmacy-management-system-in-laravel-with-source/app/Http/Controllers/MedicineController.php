<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MedicineController extends Controller
{
    public function index(Request $request)
    {
        $medicines = Medicine::with('category')
            ->when($request->q, fn ($q, $term) => $q->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('generic_name', 'like', "%{$term}%")
                    ->orWhere('barcode', $term);
            }))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('medicines.form', [
            'medicine' => new Medicine(['reorder_level' => 20]),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Medicine::create($this->validated($request));

        return redirect()->route('medicines.index')->with('ok', 'Medicine added.');
    }

    public function edit(Medicine $medicine)
    {
        return view('medicines.form', [
            'medicine' => $medicine,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Medicine $medicine)
    {
        $medicine->update($this->validated($request, $medicine));

        return redirect()->route('medicines.index')->with('ok', 'Medicine updated.');
    }

    public function destroy(Medicine $medicine)
    {
        if ($medicine->saleItems()->exists()) {
            return back()->withErrors(['delete' => 'This medicine has sales history.']);
        }

        $medicine->delete();

        return redirect()->route('medicines.index')->with('ok', 'Medicine deleted.');
    }

    private function validated(Request $request, ?Medicine $medicine = null): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:120'],
            'generic_name' => ['nullable', 'string', 'max:120'],
            'barcode' => ['required', 'string', 'max:40',
                Rule::unique('medicines', 'barcode')->ignore($medicine)],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'reorder_level' => ['required', 'integer', 'min:0'],
            'expiry_date' => ['required', 'date'],
        ]);
    }
}
