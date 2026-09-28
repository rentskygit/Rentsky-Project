@php
    // Organizar módulos en árbol: raíces y agrupados por padre
    $roots = $modules->whereNull('parent_id');
    $byParent = $modules->groupBy('parent_id');
@endphp

<div style="border:1px solid #e5e7eb; border-radius:0.5rem; padding:1rem; max-height:400px; overflow-y:auto;">
    @forelse($roots as $root)
        <div style="margin-bottom:0.75rem;">
            <label style="display:flex; align-items:center; gap:0.5rem; font-weight:600; cursor:pointer;">
                <input type="checkbox" name="modules[]" value="{{ $root->id }}"
                       {{ in_array($root->id, $selected) ? 'checked' : '' }}>
                <i class="bi {{ $root->icon }}"></i> {{ $root->name }}
            </label>

            @php $children = $byParent->get($root->id, collect()); @endphp
            @if($children->isNotEmpty())
                <div style="margin-left:2rem; margin-top:0.4rem;">
                    @foreach($children as $child)
                        <label style="display:flex; align-items:center; gap:0.5rem; padding:0.25rem 0; cursor:pointer; font-size:0.9rem;">
                            <input type="checkbox" name="modules[]" value="{{ $child->id }}"
                                   {{ in_array($child->id, $selected) ? 'checked' : '' }}>
                            <i class="bi {{ $child->icon }}"></i> {{ $child->name }}
                        </label>
                    @endforeach
                </div>
            @endif
        </div>
    @empty
        <p style="color:var(--brand-muted); font-size:0.9rem; text-align:center; padding:1rem;">
            No hay módulos activos disponibles.
        </p>
    @endforelse
</div>