@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection

@section('title', 'Crear Alumnos')
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
            <a href="{{ route('alumnos.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('alumnos.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre"
                            value="{{ request()->old('nombre') }}">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="curp">Curp</label>
                        <input type="text" class="form-control" name="curp" id="curp" placeholder="CURP"
                            value="{{ request()->old('curp') }}" maxlength="18">
                        @error('curp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="apellido_paterno">Apellido Paterno</label>
                        <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" placeholder="Apellido Paterno"
                            value="{{ request()->old('apellido_paterno') }}">
                        @error('apellido_paterno')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="apellido_materno">Apellido Materno</label>
                        <input type="text" class="form-control" name="apellido_materno" id="apellido_materno" placeholder="Apellido Materno"
                            value="{{ request()->old('apellido_materno') }}">
                        @error('apellido_materno')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email"
                            value="{{ request()->old('email') }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="grupo_id">Grupo</label>
                        <select name="grupo_id" id="grupo_id" class="form-control">
                            <option value="">Selecciona una opción</option>
                            @foreach ($grupos as $grupo)
                                <option {{ $grupo->id == old('grupo_id') ? 'selected' : '' }} value="{{ $grupo->id }}">
                                    {{ $grupo->nombre }}</option>
                            @endforeach
                        </select>
                        @error('grupo_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-responsive">Crear</button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
    <script>
        $(function() {
            $('#materias').selectpicker();

            let inputCurp = document.querySelector('#curp');
            inputCurp.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase();
            })
        })
    </script>
@endsection
