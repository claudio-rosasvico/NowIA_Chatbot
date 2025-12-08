<div class="ms-3 position-relative">
    <div class="input-group input-group-sm">
        <span class="input-group-text bg-dark text-white border-secondary">
            <i class="bi bi-building-fill-gear"></i>
        </span>
        <select wire:model.live="selectedOrgId" class="form-select form-select-sm bg-dark text-white border-secondary"
            style="max-width: 200px;">
            <option value="">-- Ver Todo --</option>
            @foreach($organizations as $org)
                <option value="{{ $org->id }}">{{ $org->name }}</option>
            @endforeach
        </select>
        @if($selectedOrgId)
            <button wire:click="clearSelection" class="btn btn-sm btn-outline-warning" title="Reset Context">
                <i class="bi bi-x-lg"></i>
            </button>
        @endif
    </div>
</div>