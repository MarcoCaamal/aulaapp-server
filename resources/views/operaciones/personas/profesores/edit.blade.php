@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
@endsection

@section('title', 'Editar Profesores')
@section('content')
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h1 class="h3 mb-0">Editar</h1>
            <a href="{{ route('profesores.index') }}" class="btn btn-outline-secondary btn-lg me-3">
                <i class='bx bx-arrow-back'></i> <span class="d-none d-md-inline-block">Regresar</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('profesores.update', ['id' => $profesor->id]) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="nombre">Nombre</label>
                        <input type="text" class="form-control" name="nombre" id="nombre" placeholder="Nombre"
                            value="{{ request()->old('nombre', $profesor->nombre) }}">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="curp">Curp</label>
                        <input type="text" class="form-control" name="curp" id="curp" placeholder="Curp"
                            value="{{ request()->old('curp', $profesor->curp) }}">
                        @error('curp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="apellido_paterno">Apellido Paterno</label>
                        <input type="text" class="form-control" name="apellido_paterno" id="apellido_paterno" placeholder="Apellido Paterno"
                            value="{{ request()->old('apellido_paterno', $profesor->apellido_paterno ) }}">
                        @error('apellido_paterno')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="apellido_materno">Apellido Materno</label>
                        <input type="text" class="form-control" name="apellido_materno" id="apellido_materno" placeholder="Apellido Materno"
                            value="{{ request()->old('apellido_materno', $profesor->apellido_materno) }}">
                        @error('apellido_materno')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="text" class="form-control" name="email" id="email" placeholder="Email"
                            value="{{ request()->old('email', $profesor->email) }}">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="materias">Materias</label>
                        <select name="materias[]" id="materias" class="form-control" multiple>
                            @foreach ($materias as $materia)
                                <option value="{{ $materia->id }}" {{ in_array($materia->id, old('materias', $profesor->materias->pluck('id')->toArray())) ? 'selected' : '' }}> {{ $materia->nombre }}</option>
                            @endforeach
                        </select>
                        @error('materias')
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>
<script>
    $(function() {
        $('#materias').selectpicker();
    })
</script>
@endsection
