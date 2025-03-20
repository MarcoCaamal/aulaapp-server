@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('title', 'Crear Grupos')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger d-flex" role="alert">
            <span class="badge badge-center rounded-pill bg-danger border-label-danger p-3 me-2"><i
                    class='bx bxs-error-circle fs-6'></i></span>
            <div class="d-flex flex-column ps-1">
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">¡Error!</h6>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Crear</h1>
            <a href="{{ route('grupos.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('grupos.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre"
                            value="{{ old('nombre') }}">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="semestre">Semestre</label>
                        <select name="id_semestre" id="semestres" class="form-select">
                            <option value="">Seleccione una opción</option>
                            @foreach ($semestres as $semestre)
                                <option {{ $semestre->id == old('id_semestre') ? 'selected' : '' }} value="{{ $semestre->id }}">{{ $semestre->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                </div>
                <button type="submit" class="btn btn-primary btn-responsive">Crear</button>
            </form>
        </div>
    </div>
@endsection
