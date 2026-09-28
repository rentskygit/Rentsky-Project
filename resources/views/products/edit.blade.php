@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Editar <span>Producto</span></h1>
        <a href="{{ route('products.index') }}" class="btn-create" style="background:#6b7280;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-error" style="background:#fee2e2; color:#991b1b; padding:1rem; border-radius:0.5rem; margin-bottom:1rem;">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <form method="POST"
          action="{{ route('products.update', $product['id']) }}"
          enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @include('products._form', [
            'categories' => $categories,
            'product' => $product,
        ])

        <div style="display:flex; gap:1rem; margin-top:1.5rem;">
            <button type="submit" class="btn-create">
                <i class="bi bi-check-lg"></i> Actualizar Producto
            </button>
            <a href="{{ route('products.index') }}" class="btn-create" style="background:#6b7280;">
                Cancelar
            </a>
        </div>
    </form>
@endsection

@push('styles')
{{-- Mismo CSS que create --}}
<style>
    .form-section {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .form-section-title i {
        color: var(--brand-secondary);
    }

    .input-group {
        display: flex;
    }

    .input-group-text {
        display: flex;
        align-items: center;
        padding: 0.6rem 0.75rem;
        background: #f9fafb;
        border: 1px solid #d1d5db;
        border-right: none;
        border-radius: 0.5rem 0 0 0.5rem;
        color: var(--brand-muted);
        font-weight: 600;
    }

    .input-group .form-control {
        border-radius: 0 0.5rem 0.5rem 0;
    }

    .form-check {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-check-input {
        width: 1.1rem;
        height: 1.1rem;
        accent-color: var(--brand-secondary);
        cursor: pointer;
    }

    .form-check-label {
        cursor: pointer;
        font-size: 0.95rem;
    }

    .existing-images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .existing-image {
        position: relative;
        display: block;
        border-radius: 0.5rem;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid #e5e7eb;
        transition: all 0.2s ease;
        aspect-ratio: 1;
    }

    .existing-image:hover {
        border-color: var(--brand-secondary);
    }

    .existing-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .existing-image input[type="checkbox"] {
        position: absolute;
        top: 0.5rem;
        left: 0.5rem;
        width: 1.2rem;
        height: 1.2rem;
        accent-color: #dc2626;
        z-index: 2;
        cursor: pointer;
    }

    .existing-image-overlay {
        position: absolute;
        inset: 0;
        background: rgba(220, 38, 38, 0.85);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.3rem;
        font-size: 0.85rem;
        font-weight: 600;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .existing-image:has(input[type="checkbox"]:checked) {
        border-color: #dc2626;
        opacity: 0.6;
    }

    .existing-image input[type="checkbox"]:checked + .existing-image-overlay {
        opacity: 1;
    }

    .existing-image:hover .existing-image-overlay {
        opacity: 0.9;
    }

    .existing-image-primary {
        position: absolute;
        bottom: 0.5rem;
        left: 0.5rem;
        background: var(--brand-secondary);
        color: white;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .image-preview {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .image-preview-item {
        position: relative;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        aspect-ratio: 1;
    }

    .image-preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .image-preview-remove {
        position: absolute;
        top: 0.35rem;
        right: 0.35rem;
        background: rgba(220, 38, 38, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 0.85rem;
        transition: all 0.15s ease;
    }

    .image-preview-remove:hover {
        background: #dc2626;
        transform: scale(1.1);
    }

    .image-preview-name {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: rgba(0, 0, 0, 0.6);
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush

@push('scripts')
<script>
    (function () {
        const input = document.getElementById('images');
        const preview = document.getElementById('imagePreview');
        if (!input || !preview) return;

        let selectedFiles = [];

        function renderPreview() {
            preview.innerHTML = '';

            selectedFiles.forEach((file, index) => {
                const reader = new FileReader();
                const item = document.createElement('div');
                item.className = 'image-preview-item';

                reader.onload = function (e) {
                    item.innerHTML = `
                        <img src="${e.target.result}" alt="${file.name}">
                        <button type="button" class="image-preview-remove" data-index="${index}" title="Quitar">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        <div class="image-preview-name">${file.name}</div>
                    `;
                };
                reader.readAsDataURL(file);
                preview.appendChild(item);
            });

            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;

            preview.querySelectorAll('.image-preview-remove').forEach(btn => {
                btn.addEventListener('click', function () {
                    const idx = parseInt(this.dataset.index);
                    selectedFiles.splice(idx, 1);
                    renderPreview();
                });
            });
        }

        input.addEventListener('change', function () {
            const files = Array.from(this.files);
            selectedFiles = selectedFiles.concat(files);
            renderPreview();
        });
    })();
</script>
@endpush