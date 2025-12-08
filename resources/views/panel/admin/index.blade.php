@extends('layouts.panel')

@section('title', 'Super Admin')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3 text-dark">Panel de Desarrollador</h1>
            <p class="text-black-50">Gestión global de usuarios, bots y consumo de tokens.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success bg-gradient text-white border-0 shadow-sm mb-4">
            <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger bg-gradient text-white border-0 shadow-sm mb-4">
            <i class="bi bi-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card bg-dark border-0 shadow-sm">
        <div class="card-header bg-transparent border-secondary border-opacity-25 py-3">
            <h5 class="card-title mb-0 h6 text-uppercase text-white-50 ls-1">Usuarios Registrados</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead class="text-secondary small text-uppercase">
                    <tr>
                        <th class="ps-4">Usuario</th>
                        <th>Email / ID</th>
                        <th class="text-center">Bots</th>
                        <th class="text-center">Tokens (Total)</th>
                        <th class="text-center">Estado</th>
                        <th class="text-end pe-4">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-white">{{ $user->name }}</div>
                                @if($user->is_super_admin)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65em;">DEV</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-25 text-white-50"
                                        style="font-size: 0.65em;">USER</span>
                                @endif
                            </td>
                            <td>
                                <div class="text-white-50">{{ $user->email }}</div>
                                <div class="small text-muted" style="font-size: 0.7em;">ID: {{ $user->id }}</div>
                            </td>
                            <td class="text-center">
                                <span
                                    class="badge bg-dark border border-secondary text-white-50 rounded-pill">{{ $user->bots_count }}</span>
                            </td>
                            <td class="text-center">
                                <span class="text-info fw-bold">{{ number_format($user->total_tokens) }}</span>
                            </td>
                            <td class="text-center">
                                @if($user->is_active)
                                    <span class="badge bg-success bg-opacity-25 text-success-emphasis"><i
                                            class="bi bi-check-circle-fill me-1"></i> Activo</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-25 text-danger-emphasis"><i
                                            class="bi bi-slash-circle-fill me-1"></i> Inactivo</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <form action="{{ route('admin.users.toggle', $user->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    @if($user->is_active)
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Desactivar usuario">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Activar usuario">
                                            <i class="bi bi-power"></i>
                                        </button>
                                    @endif
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No hay usuarios registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection