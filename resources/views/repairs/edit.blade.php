@extends('layouts.app')
@section('title', 'Ubah Riwayat Perbaikan')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('repairs.update', $repair) }}">
            @csrf @method('PUT')
            @include('repairs._form')
            <button class="btn btn-primary mt-3">Simpan Perubahan</button>
            <a href="{{ route('repairs.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
