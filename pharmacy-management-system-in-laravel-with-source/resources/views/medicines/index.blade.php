@extends('layout')
@section('title', 'Medicines')

@section('content')
<div class="bar">
    <h1>Medicines</h1>
    <form method="get" class="search">
        <input name="q" value="{{ request('q') }}" placeholder="Name, generic or barcode">
        <button class="btn ghost">Search</button>
    </form>
    <a class="btn" href="{{ route('medicines.create') }}">+ Add medicine</a>
</div>

<div class="card">
<table>
    <thead>
        <tr><th>Medicine</th><th>Category</th><th>Barcode</th><th class="num">Price</th>
            <th class="num">Stock</th><th>Expiry</th><th></th></tr>
    </thead>
    @foreach ($medicines as $m)
        <tr>
            <td>{{ $m->name }}<br><span class="muted small">{{ $m->generic_name }}</span></td>
            <td>{{ $m->category->name }}</td>
            <td class="mono">{{ $m->barcode }}</td>
            <td class="num">{{ number_format($m->price, 2) }}</td>
            <td class="num">{{ $m->stock }}</td>
            <td><span class="pill {{ $m->status }}">{{ $m->expiry_date->format('M Y') }}</span></td>
            <td class="actions">
                <a href="{{ route('medicines.edit', $m) }}">Edit</a>
                <form method="post" action="{{ route('medicines.destroy', $m) }}"
                      onsubmit="return confirm('Delete {{ addslashes($m->name) }}?')">
                    @csrf @method('delete')
                    <button class="link bad">Delete</button>
                </form>
            </td>
        </tr>
    @endforeach
</table>
</div>
{{ $medicines->links('partials.pager') }}
@endsection
