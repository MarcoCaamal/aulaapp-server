@extends('layouts.app-layout')

@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.css" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('title', 'Ciclos')

@section('content')
    @if (session('success'))
        <div class="alert alert-success d-flex" role="alert">
            <span class="badge badge-center rounded-pill bg-success border-label-success p-3 me-2">
                <i class='bx bxs-check-circle fs-6'></i>
            </span>
            <div class="d-flex flex-column ps-1">
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">!Éxito!</h6>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

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

    <div class="row">
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h1 class="h4">Ciclos</h1>
                    <a href="{{ route('ciclos.create') }}" class="btn btn-primary btn-lg me-3 text-white">
                        <i class='bx bx-plus-circle'></i> <span class="d-none d-md-inline-block">Crear</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="container p-3">
                    @if (!empty($ciclos))
                        <table id="tabla" class="display table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Activo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ciclos as $ciclo)
                                    <tr>
                                        <td>{{ $ciclo->nombre }}</td>
                                        <td>{{ $ciclo->fecha_inicio }}</td>
                                        <td>{{ $ciclo->fecha_fin }}</td>
                                        <td>
                                            @if ($ciclo->is_activo)
                                                <span class="badge bg-success">Si</span>
                                            @else
                                                <span class="badge bg-danger">No</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-info dropdown-toggle"
                                                    data-bs-toggle="dropdown" aria-expanded="false">Seleccionar</button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('ciclos.edit', ['id' => $ciclo->id]) }}">
                                                            <i class='bx bx-edit-alt me-2'></i>Editar
                                                        </a>
                                                    </li>
                                                    @if ($ciclo->is_activo)
                                                        <form action="{{ route('ciclos.desactivate', ['id' => $ciclo->id]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class='bx bx-block me-2'></i>Desactivar
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form action="{{ route('ciclos.activate', ['id' => $ciclo->id]) }}" method="POST">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item">
                                                                <i class='bx bx-check-circle me-2'></i>Activar
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <li>
                                                        <form class="formEliminar" method="POST">
                                                            <input type="hidden" class="urlAPI"
                                                                value="/ciclos/delete/{{ $ciclo->id }}">
                                                            <button type="submit" class="dropdown-item">
                                                                <i class='bx bxs-trash me-2'></i> Eliminar
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-center text-muted mb-0">Sin Registros</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs5/dt-1.13.1/datatables.min.js"></script>
    @vite(['resources/js/alerts.js'])

    <script>
        $(document).ready(function() {
            $('#tabla').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.1/i18n/es-ES.json"
                }
            });
        });
    </script>
@endsection
