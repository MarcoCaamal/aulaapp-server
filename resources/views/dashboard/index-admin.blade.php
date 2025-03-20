@extends('layouts.app-layout')

@section('title', 'Dashboard')

@section('content')
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex flex-column align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-success text-white p-2 rounded mb-1">
                                <i class="fa-solid fa-graduation-cap" style="font-size: 25px"></i>
                            </div>
                            <h4 class="fw-semibold d-block ms-2">Alumnos</h4>
                        </div>
                        <h4 class="card-title h2 border-bottom border-5 border-success mt-4">30</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex flex-column align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-info text-white p-2 rounded mb-1">
                                <i class="fa-solid fa-chalkboard-user" style="font-size: 25px"></i>
                            </div>
                            <h4 class="fw-semibold d-block ms-2">Profesores</h4>
                        </div>
                        <h4 class="card-title h2 border-bottom border-5 border-info mt-4">18</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card">
                <div class="card-body pb-0">
                    <div class="d-flex flex-column align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white p-2 rounded mb-1">
                                <i class="fa-solid fa-graduation-cap" style="font-size: 25px"></i>
                            </div>
                            <h4 class="fw-semibold d-block ms-2">Alumnos Asesores</h4>
                        </div>
                        <h4 class="card-title h2 border-bottom border-5 border-primary mt-4">30</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Total De Asistencias de Febrero</h4>
                </div>
                <div class="body">
                    <div class="container">
                        <div id="chartAsistencias"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Asesores Por Materia</h4>
                </div>
                <div class="body">
                    <div class="container">
                        <div id="chartPastel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard-charts.js'])
@endsection
