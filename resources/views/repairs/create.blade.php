@extends('layouts.app')
@section('title', 'Catat Kerusakan')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('repairs.store') }}">
            @csrf
            @include('repairs._form')
            <button class="btn btn-success mt-3">Simpan</button>
            <a href="{{ route('repairs.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
