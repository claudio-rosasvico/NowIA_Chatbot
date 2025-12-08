@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-12 mb-4">
            <h2 class="h4 text-light border-bottom border-secondary pb-2 mb-4">{{ __('Dashboard') }}</h2>
        </div>

        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="display-5 fw-bold text-white mb-3">¡Hola, {{ Auth::user()->name }}!</h1>
                    <p class="lead text-light opacity-75 mb-4">Bienvenido a tu panel de control de NowIA.</p>

                    <div class="d-flex gap-3">
                        <a href="{{ route('panel.sources') }}" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-folder-plus me-2"></i> Gestionar Fuentes
                        </a>
                        <a href="{{ route('chat') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-chat-dots me-2"></i> Ir al Chat
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection