@extends('layouts.app-layout')

@section('styles')
    @vite(['resources/css/drop-zone.css'])
@endsection

@section('title', 'Carga Masiva de Profesores')
@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Cargar Masiva de Profesores</h1>
            <a href="{{ route('profesores.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <div class="card-body">
            <form action="{{ route('profesores.carga-masiva-cargar')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="drop-zone mx-auto my-0">
                    <span class="drop-zone__prompt">Arrastra el archivo aquí o click para seleccionar</span>

                    <input class="drop-zone__input" type="file" name="archivo_carga_masiva">
                </div>
                <button type="submit" class="btn btn-primary">Subir</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/drop-zone.js'])
@endsection
