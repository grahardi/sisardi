@extends('layouts.app')
@section('title', 'Ubah Aset')
@section('content')
<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('assets.update', $asset) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            @include('assets._form', ['asset' => $asset])
            <button class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan Perubahan</button>
            <a href="{{ route('assets.index') }}" class="btn btn-secondary mt-3">Batal</a>
        </form>
    </div>
</div>
@endsection
