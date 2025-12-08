@extends('layouts.panel')
@section('title', 'Métricas de Uso')

@section('content')
    <div class="row g-4 mb-4">
        <!-- Latencia promedio -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 bg-dark text-white">
                <div class="card-body">
                    <div class="text-white-50 small mb-1"><i class="bi bi-stopwatch"></i> Latencia media (7d)</div>
                    <div class="h3 mb-0 fw-bold">{{ $avgLatency }} <span class="fs-6 fw-normal text-muted">ms</span></div>
                </div>
            </div>
        </div>
        <!-- Tokens IN -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 bg-dark text-white">
                <div class="card-body">
                    <div class="text-white-50 small mb-1"><i class="bi bi-arrow-down-circle"></i> Tokens Entrada (7d)</div>
                    <div class="h3 mb-0 fw-bold text-info">{{ number_format($tokIn) }}</div>
                </div>
            </div>
        </div>
        <!-- Tokens OUT -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 bg-dark text-white">
                <div class="card-body">
                    <div class="text-white-50 small mb-1"><i class="bi bi-arrow-up-circle"></i> Tokens Salida (7d)</div>
                    <div class="h3 mb-0 fw-bold text-brand-secondary">{{ number_format($tokOut) }}</div>
                </div>
            </div>
        </div>
        <!-- Total Eventos -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100 border-0 bg-dark text-white">
                <div class="card-body">
                    <div class="text-white-50 small mb-1"><i class="bi bi-activity"></i> Total Interacciones (14d)</div>
                    <div class="h3 mb-0 fw-bold">{{ array_sum($dailyCounts) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráfico de Línea -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm border-0 bg-dark text-white">
                <div class="card-header bg-transparent border-secondary border-opacity-25 py-3">
                    <h5 class="card-title mb-0 h6 text-uppercase text-white-50 ls-1">Interacciones Diarias (Últimos 14 días)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="chartDaily" height="100"></canvas>
                </div>
            </div>
        </div>

        <!-- Tabla de Proveedores -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0 bg-dark text-white h-100">
                <div class="card-header bg-transparent border-secondary border-opacity-25 py-3">
                    <h5 class="card-title mb-0 h6 text-uppercase text-white-50 ls-1">Por Modelo IA</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle">
                            <thead class="text-secondary small text-uppercase">
                                <tr>
                                    <th class="ps-4">Modelo</th>
                                    <th class="text-center">Uso</th>
                                    <th class="text-end pe-4">Latencia</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($byProvider as $row)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold">{{ ucfirst($row->provider ?? 'Unknown') }}</div>
                                            <div class="small text-muted" style="font-size: 0.75rem;">IA Generativa</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary bg-opacity-25 text-light">{{ $row->c }}</span>
                                        </td>
                                        <td class="text-end pe-4 text-white-50">
                                            {{ (int) $row->ms }} ms
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">Sin datos registrados aún.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = @json($dailyLabels);
        const data = @json($dailyCounts);

        new Chart(document.getElementById('chartDaily'), {
            type: 'line',
            data: {
                labels,
                datasets: [{
                    label: 'Interacciones',
                    data,
                    tension: 0.4,
                    borderColor: '#f99f13', // Brand Secondary
                    backgroundColor: 'rgba(249, 159, 19, 0.1)',
                    fill: true,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#f99f13',
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: '#333',
                        borderWidth: 1,
                        padding: 10,
                        displayColors: false,
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.05)' },
                        ticks: { color: '#aaa', precision: 0 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#aaa' }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
            }
        });
    </script>
@endpush