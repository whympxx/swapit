@extends('layouts.app')

@section('title', 'Edit Barang')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-pen"></i></span> Edit Barang</h1>
        <p>Perbarui info barangmu biar makin menarik.</p>
    </div>

    <div class="form-layout">
        <div class="form-container">
            <form method="POST" action="{{ route('items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('items._form', ['item' => $item])

                <div class="form-actions">
                    <a href="{{ route('items.mine') }}" class="btn">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-floppy-disk"></i> Simpan Perubahan</button>
                </div>
            </form>
        </div>
        @include('items._tips')
    </div>
@endsection
