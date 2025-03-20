@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endsection

@section('title', 'Crear Horarios')

@section('content')

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Crear</h1>
            <a href="{{ route('horarios.index', ['idProfesor' => $profesor->id]) }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <form id="formulario" method="POST" action="{{ route('horarios.store', ['idProfesor' => $profesor->id]) }}">
            <input type="hidden" value="{{ $profesor->id }}" id="profesorId" />
            <div class="card-body">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="lugar">Lugar</label>
                        <input type="text" class="form-control" name="lugar" id="lugar" placeholder="Lugar"
                            value="{{ request()->old('lugar') }}">
                        @error('lugar')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="materia">Materia</label>
                        <select name="materia_id" id="materia" class="form-select">
                            <option value="">Seleccione una opción</option>
                            @foreach ($materias as $materia)
                                <option {{ $materia->id == old('materia_id' ? 'selected' : '') }}
                                    value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                            @endforeach
                        </select>
                        @error('materia_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Dia</label>
                        <select class="form-select select-dia" name="dia_semana">
                            <option value="" selected disabled>Selecione una opción</option>
                            @foreach ($diasSemana as $diaSemana)
                                <option {{ "data-dia-value='{$diaSemana['value']}'" }} value="{{ $diaSemana['value'] }}">
                                    {{ $diaSemana['name'] }}</option>
                            @endforeach
                        </select>
                        @error('dia_semana')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" >Hora Inicio</label>
                        <input class="form-control hora-inicio" name="hora_inicio" value="{{ old('hora_inicio')}}"/>
                        @error('hora-incio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Hora Fin</label>
                        <input class="form-control hora-fin" name="hora_fin" value="{{ old('hora_fin') }}"/>
                        @error('hora_fin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-responsive">Crear</button>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/date-pickers.js'])
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>
@endsection
