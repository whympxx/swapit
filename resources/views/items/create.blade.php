@extends('layouts.app')

@section('title', 'Posting Barang')

@section('content')
    <div class="page-header">
        <h1><span class="ph-icon"><i class="fas fa-plus"></i></span> Posting Barang</h1>
        <p>Barang nganggur → barang incaran. Isi detailnya, langsung tayang.</p>
    </div>

    <div class="form-layout">
        <div class="form-container">
            <form method="POST" action="{{ route('items.store') }}" enctype="multipart/form-data">
                @csrf
                @include('items._form')

                <div class="form-actions">
                    <a href="{{ route('items.mine') }}" class="btn">Batal</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-rocket"></i> Posting Sekarang</button>
                </div>
            </form>
        </div>
        @include('items._tips')
    </div>
@endsection
