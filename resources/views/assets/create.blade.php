@extends('layouts.app')
@section('title', 'Tambah Aset')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('assets.store') }}" enctype="multipart/form-data">
            @csrf
            @include('assets._form', ['nextKode' => $nextKode ?? null])
            <button class="btn btn-success mt-3"><i class="bi bi-save"></i> Simpan Aset</button>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
