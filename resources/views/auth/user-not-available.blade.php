@extends('layouts.guest-layout')

@section('title', 'No Disponible')

@section('content')
    <div class="container d-flex flex-column justify-content-center align-items-center vh-100">
        <img class="mb-4" src="/assets/img/logos/logo.svg" alt="">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">No disponible</h5>
                <p class="card-text">La funcionalidad para su usuario no está disponible. Si usted es Profesor o Alumno prueba en la aplicación móvil.</p>
                <p class="card-text"><small class="text-muted"><b>Atentamente:</b> El equipo de AsesoraT</small></p>
                <form action="{{ route('login.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-primary">Cerrar Sesión</button>
                </form>
            </div>
        </div>
    </div>
@endsection
