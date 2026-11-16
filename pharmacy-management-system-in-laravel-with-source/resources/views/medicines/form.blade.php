@extends('layout')
@section('title', $medicine->exists ? 'Edit medicine' : 'Add medicine')

@section('content')
<h1>{{ $medicine->exists ? 'Edit medicine' : 'Add medicine' }}</h1>

<form class="card form" method="post"
      action="{{ $medicine->exists ? route('medicines.update', $medicine) : route('medicines.store') }}">
    @csrf
    @if ($medicine->exists) @method('put') @endif

    <label>Name
        <input name="name" value="{{ old('name', $medicine->name) }}" required>
    </label>
    <label>Generic name
        <input name="generic_name" value="{{ old('generic_name', $medicine->generic_name) }}">
    </label>
    <label>Category
        <select name="category_id" required>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}" @selected(old('category_id', $medicine->category_id) == $c->id)>
                    {{ $c->name }}
                </option>
            @endforeach
        </select>
    </label>
    <label>Barcode
        <input name="barcode" value="{{ old('barcode', $medicine->barcode) }}" required>
    </label>
    <div class="row">
        <label>Price
            <input type="number" step="0.01" name="price" value="{{ old('price', $medicine->price) }}" required>
        </label>
        <label>Stock
            <input type="number" name="stock" value="{{ old('stock', $medicine->stock) }}" required>
        </label>
        <label>Reorder level
            <input type="number" name="reorder_level" value="{{ old('reorder_level', $medicine->reorder_level) }}" required>
        </label>
    </div>
    <label>Expiry date
        <input type="date" name="expiry_date"
               value="{{ old('expiry_date', $medicine->expiry_date?->toDateString()) }}" required>
    </label>
    <button class="btn">Save</button>
</form>
@endsection
