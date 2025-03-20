@extends('layouts.guest-layout')

@section('title', 'Verificar Email')

@section('content')
    @if (session('success'))
        <div class="alert alert-success d-flex justify-content-center" role="alert">
            <span class="badge badge-center rounded-pill bg-success border-label-success p-3 me-2">
                <i class='bx bxs-check-circle fs-6'></i>
            </span>
            <div class="d-flex flex-column ps-1">
                <h6 class="alert-heading d-flex align-items-center fw-bold mb-1">!Éxito!</h6>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    <div class="d-flex justify-content-center align-items-center vh-100 flex-column gap-3">
        <img src="/assets/img/logos/logo.svg" alt="Logo Sitio">
        <div class="card text-center w-50">
            <div class="card-body">
                <h5 class="card-title">Cuenta No Verificada</h5>
                <p class="card-text">Gracias por registrarte! Antes de comenzar, ¿podría verificar su dirección de correo
                    electrónico haciendo clic en el enlace que le acabamos de enviar? Si no recibiste el correo electrónico,
                    con gusto te enviaremos otro.</p>
                <p class="card-text"><small class="text-muted"><b>Atentamente:</b> El equipo de AsesoraT</small></p>
                <div class="d-flex justify-content-between">
                    <form action="{{ route('verification.send') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Reenviar Correo de Verificación</button>
                    </form>
                    <form action="{{ route('login.logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary">Cerrar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
