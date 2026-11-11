@php
    $selected = collect($selected ?? [])->map(fn($id) => (int) $id)->toArray();
@endphp

<div class="module-tree" id="moduleTree">
    @foreach($modules as $module)
        @if($module->children->isEmpty())
            @php $isChecked = in_array((int) $module->id, $selected, true); @endphp
            <label class="module-item {{ $isChecked ? 'checked' : '' }}">
                <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                       data-module-id="{{ $module->id }}"
                       {{ $isChecked ? 'checked' : '' }}>
                <i class="bi {{ $module->icon }}"></i>
                <span>{{ $module->name }}</span>
            </label>
        @else
            @php $parentChecked = in_array((int) $module->id, $selected, true); @endphp
            <div class="module-group">
                <label class="module-item parent {{ $parentChecked ? 'checked' : '' }}">
                    <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                           data-module-id="{{ $module->id }}"
                           data-group="group-{{ $module->id }}"
                           {{ $parentChecked ? 'checked' : '' }}>
                    <i class="bi {{ $module->icon }}"></i>
                    <span>{{ $module->name }}</span>
                </label>

                <div class="module-children">
                    @foreach($module->children as $child)
                        @php $childChecked = in_array((int) $child->id, $selected, true); @endphp
                        <label class="module-item {{ $childChecked ? 'checked' : '' }}">
                            <input type="checkbox" name="modules[]" value="{{ $child->id }}"
                                   data-module-id="{{ $child->id }}"
                                   data-parent="group-{{ $module->id }}"
                                   {{ $childChecked ? 'checked' : '' }}>
                            <i class="bi {{ $child->icon }}"></i>
                            <span>{{ $child->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>

@push('styles')
<style>
    .module-tree { display: flex; flex-direction: column; gap: 0.5rem; }
    .module-group { margin-bottom: 0.5rem; }

    .module-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.7rem 0.9rem;
        border: 1px solid var(--border);
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.88rem;
        font-weight: 500;
        background: white;
        transition: all 0.15s ease;
        user-select: none;
    }
    .module-item:hover { border-color: var(--border-strong); background: var(--surface); }
    .module-item.checked { border-color: var(--brand-primary); background: var(--surface-alt); }
    .module-item.parent { font-weight: 600; }
    .module-item input[type="checkbox"] {
        accent-color: var(--brand-secondary);
        cursor: pointer;
        flex-shrink: 0;
        width: 16px;
        height: 16px;
    }
    .module-item i {
        color: var(--brand-muted);
        font-size: 0.95rem;
        flex-shrink: 0;
        width: 18px;
        text-align: center;
    }
    .module-item.checked i { color: var(--brand-secondary); }

    .module-children {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        margin-left: 1.5rem;
        margin-top: 0.5rem;
        padding-left: 1rem;
        border-left: 2px solid var(--border);
    }
    .module-children .module-item { font-size: 0.83rem; }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const tree = document.getElementById('moduleTree');
    if (!tree) return;

    tree.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
        cb.addEventListener('change', function () {
            const label = this.closest('.module-item');
            if (label) label.classList.toggle('checked', this.checked);
        });
    });

    tree.querySelectorAll('input[data-parent]').forEach(function (child) {
        child.addEventListener('change', function () {
            const parentGroup = this.dataset.parent;
            const parentCb = tree.querySelector('input[data-group="' + parentGroup + '"]');
            if (!parentCb) return;

            if (this.checked) {
                if (!parentCb.checked) {
                    parentCb.checked = true;
                    const parentLabel = parentCb.closest('.module-item');
                    if (parentLabel) parentLabel.classList.add('checked');
                }
            } else {
                const siblings = tree.querySelectorAll('input[data-parent="' + parentGroup + '"]');
                const anyChecked = Array.prototype.some.call(siblings, function (cb) {
                    return cb.checked;
                });
                if (!anyChecked && parentCb.checked) {
                    parentCb.checked = false;
                    const parentLabel = parentCb.closest('.module-item');
                    if (parentLabel) parentLabel.classList.remove('checked');
                }
            }
        });
    });
})();
</script>
@endpush