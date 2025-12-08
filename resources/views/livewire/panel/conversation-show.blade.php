<div class="row justify-content-center">
  <div class="col-md-10">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3 text-white">Detalle de Conversación #{{ $conversation->id }}</h1>
      <a href="{{ route('panel.conversations') }}" class="btn btn-outline-light btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
      </a>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-header bg-dark border-secondary d-flex justify-content-between text-white-50 small">
        <span>
          <i class="bi bi-calendar3"></i> {{ $conversation->created_at->format('d/m/Y H:i') }}
        </span>
        <span>
          <i class="bi bi-hash"></i> Canal: {{ ucfirst($conversation->channel) }}
        </span>
      </div>
      <div class="card-body" style="background-color: var(--primary-color);">
        @if($conversation->messages->isEmpty())
          <div class="text-center text-muted py-5">
            <i class="bi bi-chat-square-dots display-4 mb-3 d-block"></i>
            Esta conversación no tiene mensajes.
          </div>
        @else
          <div class="chat-history">
            @foreach($conversation->messages as $msg)
              <div class="mb-4 {{ $msg->role === 'user' ? 'text-end' : 'text-start' }}">
                <div
                  class="d-inline-block text-start p-3 rounded-3 shadow-sm border {{ $msg->role === 'user' ? 'bg-brand-primary border-secondary' : 'bg-dark border-secondary' }}"
                  style="max-width: 80%;">
                  <div class="small fw-bold mb-1 {{ $msg->role === 'user' ? 'text-brand-secondary' : 'text-success' }}">
                    {{ $msg->role === 'user' ? 'Usuario' : 'Asistente' }}
                  </div>
                  <div class="text-light text-break" style="white-space: pre-wrap;">{{ $msg->content }}</div>
                  <div class="mt-2 small text-muted d-flex justify-content-between align-items-center">
                    <span>{{ $msg->created_at->format('H:i:s') }}</span>
                    @if($msg->role === 'assistant' && isset($msg->meta['tokens_in']))
                      <span class="badge bg-secondary bg-opacity-25 text-white-50" style="font-size: 0.7em;">
                        <i class="bi bi-cpu"></i> {{ $msg->meta['tokens_in'] + $msg->meta['tokens_out'] }} tokens
                      </span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>
    </div>

    @php
      // Calcular métricas aproximadas si existen eventos relacionados
      // (Asumiendo que AnalyticsEvent tiene conversation_id, podríamos traerlos si la relación existiera en el modelo,
      //  pero por ahora sumaremos manualmente si queremos o solo mostramos estructura)
      // Para esta versión v1, solo mostraremos la estructura visual del chat.
    @endphp

  </div>
</div>