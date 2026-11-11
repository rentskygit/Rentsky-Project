@php $c = $category ?? null; @endphp
<div class="row">
    <div class="form-group" style="grid-column: span 1;">
        <label class="form-label">Name *</label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $c->name ?? '') }}" required>
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-group" style="grid-column: span 1;">
        <label class="form-label">Icon (Bootstrap Icons)</label>
        <input type="text" name="icon" class="form-control" value="{{ old('icon', $c->icon ?? 'bi-house') }}">
        <small class="text-muted">E.g. bi-house, bi-building</small>
    </div>
</div>
<div class="form-group">
    <label class="form-label">Description</label>
    <input type="text" name="description" class="form-control" value="{{ old('description', $c->description ?? '') }}">
</div>
<div class="row">
    <div class="form-group" style="grid-column: span 1;">
        <label class="form-label">Order *</label>
        <input type="number" name="order" min="0" class="form-control"
               value="{{ old('order', $c->order ?? 0) }}" required>
    </div>
    <div class="form-group" style="grid-column: span 1;">
        <label class="form-label" style="display:flex;align-items:center;gap:.5rem;">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $c->is_active ?? true) ? 'checked' : '' }}>
            Active
        </label>
    </div>
</div>