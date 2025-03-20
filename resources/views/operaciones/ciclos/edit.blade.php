@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('title', 'Editar Ciclos')

@section('content')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Editar</h1>
            <a href="{{ route('ciclos.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('ciclos.update', [$ciclo->id]) }}">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre"
                            value="{{ old('nombre', $ciclo->nombre) }}">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="fecha_inicio">Fecha Inicio</label>
                        <input type="text" class="form-control" name="fecha_inicio" id="fecha_inicio"
                            placeholder="Fecha Inicio" value="{{ old('fecha_inicio', $ciclo->fecha_inicio) }}">
                        @error('fecha_inicio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="fecha_fin">Fecha Fin</label>
                        <input type="text" class="form-control" name="fecha_fin" id="fecha_fin" placeholder="Fecha Fin"
                            value="{{ old('fecha_fin', $ciclo->fecha_fin) }}">
                        @error('fecha_fin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-responsive">Editar</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/date-pickers.js'])
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
@endsection
