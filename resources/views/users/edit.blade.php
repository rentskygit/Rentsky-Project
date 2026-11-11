@extends('layouts.app')
@section('title', 'Edit User')

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
    .module-item i { color: var(--brand-muted); font-size: 0.95rem; flex-shrink: 0; width: 18px; text-align: center; }
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

    .role-from-badge {
        margin-left: auto;
        font-size: 0.65rem;
        font-weight: 600;
        background: #dbeafe;
        color: #1e40af;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit <span>User</span></h1>
        <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-person-gear text-muted"></i>
                <span>Basic information</span>
            </div>
            <div style="padding:1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:0.5rem;">
                        <input type="checkbox" name="is_super_admin" value="1" {{ $user->is_super_admin ? 'checked' : '' }}>
                        <strong>Super Administrator</strong> (full access, ignores all permissions)
                    </label>
                </div>
            </div>
        </div>

        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-shield-lock text-muted"></i>
                <span>Roles</span>
            </div>
            <div style="padding:1.5rem;">
                <input type="hidden" name="roles_present" value="1">

                @if($roles->isEmpty())
                    <p style="color:var(--brand-muted);">No roles created yet.</p>
                @else
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:0.75rem;">
                        @foreach($roles as $role)
                            <label style="display:flex;align-items:center;gap:0.5rem;padding:0.6rem;border:1px solid var(--border);border-radius:0.5rem;cursor:pointer;">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}>
                                <div>
                                    <div style="font-weight:600;font-size:0.9rem;">{{ $role->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--brand-muted);">
                                        {{ $role->description ?? 'No description' }}
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <p style="font-size:0.8rem;color:var(--brand-muted);margin-top:0.85rem;">
                        The <strong>Client</strong> role is automatically assigned and cannot be removed.
                    </p>
                @endif
            </div>
        </div>

        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-grid-3x3-gap text-muted"></i>
                <span>Module access</span>
            </div>
            <div style="padding:1.5rem;">
                <input type="hidden" name="modules_present" value="1">

                <p style="font-size:0.85rem;color:var(--brand-muted);margin-bottom:1.25rem;line-height:1.6;">
                    Check the modules this user should have access to.
                    Modules marked <strong>From role</strong> come from the user's assigned roles — uncheck them to deny access for this user only.
                    Checking a submodule will automatically check its parent.
                </p>

                @if($modules->isEmpty())
                    <p style="color:var(--brand-muted);">No modules available.</p>
                @else
                    <div class="module-tree" id="moduleTree">
                        @foreach($modules as $module)
                            @if($module->children->isEmpty())
                                @php
                                    $isChecked = in_array($module->id, $selectedModules);
                                    $fromRole = in_array($module->id, $roleModuleIds);
                                @endphp
                                <label class="module-item {{ $isChecked ? 'checked' : '' }}">
                                    <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                           data-module-id="{{ $module->id }}"
                                           {{ $isChecked ? 'checked' : '' }}>
                                    <i class="bi {{ $module->icon }}"></i>
                                    <span>{{ $module->name }}</span>
                                    @if($fromRole)
                                        <span class="role-from-badge">From role</span>
                                    @endif
                                </label>
                            @else
                                @php
                                    $parentChecked = in_array($module->id, $selectedModules);
                                    $parentFromRole = in_array($module->id, $roleModuleIds);
                                @endphp
                                <div class="module-group">
                                    <label class="module-item parent {{ $parentChecked ? 'checked' : '' }}">
                                        <input type="checkbox" name="modules[]" value="{{ $module->id }}"
                                               data-module-id="{{ $module->id }}"
                                               data-group="group-{{ $module->id }}"
                                               {{ $parentChecked ? 'checked' : '' }}>
                                        <i class="bi {{ $module->icon }}"></i>
                                        <span>{{ $module->name }}</span>
                                        @if($parentFromRole)
                                            <span class="role-from-badge">From role</span>
                                        @endif
                                    </label>

                                    <div class="module-children">
                                        @foreach($module->children as $child)
                                            @php
                                                $childChecked = in_array($child->id, $selectedModules);
                                                $childFromRole = in_array($child->id, $roleModuleIds);
                                            @endphp
                                            <label class="module-item {{ $childChecked ? 'checked' : '' }}">
                                                <input type="checkbox" name="modules[]" value="{{ $child->id }}"
                                                       data-module-id="{{ $child->id }}"
                                                       data-parent="group-{{ $module->id }}"
                                                       {{ $childChecked ? 'checked' : '' }}>
                                                <i class="bi {{ $child->icon }}"></i>
                                                <span>{{ $child->name }}</span>
                                                @if($childFromRole)
                                                    <span class="role-from-badge">From role</span>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn-create">
                <i class="bi bi-check-lg"></i> Save changes
            </button>
            <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
                Cancel
            </a>
        </div>
    </form>
@endsection

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