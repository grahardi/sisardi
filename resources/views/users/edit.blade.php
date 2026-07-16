@extends('layouts.app')
@section('title', 'Ubah User')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf @method('PUT')
            @include('users._form')
            <button class="btn btn-primary mt-3">Simpan Perubahan</button>
            <a href="{{ route('users.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
