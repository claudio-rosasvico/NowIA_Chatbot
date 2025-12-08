{{-- <div>
  <div id="messages" class="mb-3" style="max-height: 60vh; overflow:auto;">
    @foreach ($messages as $m)
      <div class="mb-2">
        <strong class="{{ $m['role'] === 'user' ? 'text-primary' : 'text-success' }}">
{{ $m['role'] === 'user' ? 'Tú' : 'Asistente' }}:
</strong>
<div class="whitespace-pre-wrap">{{ $m['content'] }}</div>
</div>
@endforeach


<div id="assistant-live" style="display:none" class="mb-2">
    <strong class="text-success">Asistente:</strong>
    <div id="assistant-live-text" class="whitespace-pre-wrap"></div>
</div>
</div>

<form id="chatForm" onsubmit="return handleStreamSubmit(event)">
    <div class="input-group">
        <textarea id="chatInput" class="form-control" rows="2" placeholder="Escribí tu pregunta..."></textarea>
        <button class="btn btn-primary">Enviar</button>
    </div>
</form>
</div>
--}}

<div class="card">
    <div class="card-body" style="max-height: 420px; overflow:auto;">
        @foreach ($this->messages as $m)
            <div class="mb-2">
                <strong class="text-{{ $m['role'] === 'user' ? 'primary' : 'success' }}">
                    {{ $m['role'] === 'user' ? 'Vos' : 'Asistente' }}:
                </strong>
                <div class="chat-content" data-role="{{ $m['role'] }}">{{ $m['content'] }}</div>
            </div>
        @endforeach

        <!-- Typing Indicator -->
        <div wire:loading wire:target="send" class="mb-2">
            <strong class="text-success">Asistente:</strong>
            <div class="text-muted fst-italic">
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Escribiendo...
            </div>
        </div>
    </div>
    <div class="card-footer">
        <form wire:submit.prevent="send" class="d-flex gap-2">
            <input class="form-control" wire:model.defer="input" placeholder="Escribí tu mensaje...">
            <button class="btn btn-primary">Enviar</button>
        </form>
    </div>
</div>
@push('scripts')
    
@endpush