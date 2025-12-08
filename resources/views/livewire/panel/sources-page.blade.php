<div>
    @if (session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Nueva fuente</h5>

                    <form wire:submit="save" class="vstack gap-3" enctype="multipart/form-data">
                        <div>
                            <label class="form-label">Tipo</label>
                            <select class="form-select" wire:model.live="type">
                                <option value="text">Texto</option>
                                <option value="url">URL</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>

                        @switch($type)
                            @case('text')
                                <div wire:key="type-text">
                                    <label class="form-label">Contenido</label>
                                    <textarea class="form-control" rows="6" wire:model.defer="text_content"></textarea>
                                </div>
                            @break

                            @case('url')
                                <div wire:key="type-url">
                                    <label class="form-label">URL</label>
                                    <input type="url" class="form-control" placeholder="https://..." wire:model.defer="url">
                                </div>
                            @break

                            @case('pdf')
                                <div wire:key="type-pdf">
                                    <label class="form-label">Archivo PDF</label>
                                    <input type="file" class="form-control" wire:model="pdf" accept="application/pdf">
                                    <div class="form-text">Máximo 20MB.</div>
                                    @error('pdf')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            @break

                        @endswitch

                        <button class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                            <span wire:loading.remove wire:target="save">Guardar</span>
                            <span wire:loading wire:target="save"><span class="spinner-border spinner-border-sm"></span>
                                Guardando…</span>
                        </button>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Fuentes</h5>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Tipo</th>
                                    <th>Estado / Progreso</th>
                                    <th>Acciones</th>

                                </tr>
                            </thead>
                            <tbody wire:poll.5s>
                                @forelse ($sources as $s)
                                    <tr>
                                        <td>{{ $s->id }}</td>
                                        <td>{{ $s->title ?? '—' }}</td>
                                        <td><span class="badge bg-secondary">{{ $s->type }}</span></td>
                                        <td>
                                            @if ($s->status === 'ready')
                                                <span class="badge text-bg-success">Ready</span>
                                            @elseif($s->status === 'error')
                                                <span class="badge text-bg-danger">Error</span>
                                                <div class="text-danger small mt-1">{{ $s->error }}</div>
                                            @else
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <span class="text-muted small">Procesando...</span>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                @if ($s->status !== 'ready')
                                                    <button wire:click="process({{ $s->id }})"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Reintentar">
                                                        <i class="bi bi-arrow-clockwise"></i>
                                                    </button>
                                                @endif
                                                <button wire:click="delete({{ $s->id }})"
                                                    wire:confirm="¿Estás seguro de eliminar esta fuente?"
                                                    class="btn btn-sm btn-outline-danger" title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox me-1"></i>
                                            Aún no cargaste fuentes. Subí un PDF o ingresá una URL para empezar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $sources->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
