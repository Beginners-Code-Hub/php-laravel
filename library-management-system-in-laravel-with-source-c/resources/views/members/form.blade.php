@extends('layout')
@section('title', $member->exists ? 'Edit member' : 'Add member')

@section('content')
<h1>{{ $member->exists ? 'Edit member' : 'Add member' }}</h1>

<form class="card form" method="post"
      action="{{ $member->exists ? route('members.update', $member) : route('members.store') }}">
    @csrf
    @if ($member->exists) @method('put') @endif
    <label>Member code (printed as a QR on the card)
        <input name="code" value="{{ old('code', $member->code) }}" placeholder="MB-1006" required>
    </label>
    <label>Name
        <input name="name" value="{{ old('name', $member->name) }}" required>
    </label>
    <label>Email
        <input type="email" name="email" value="{{ old('email', $member->email) }}">
    </label>
    <label>Phone
        <input name="phone" value="{{ old('phone', $member->phone) }}">
    </label>
    <div class="actions">
        <a class="btn ghost" href="{{ route('members.index') }}">Cancel</a>
        <button class="btn">Save</button>
    </div>
</form>
@endsection
