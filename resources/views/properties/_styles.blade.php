<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .form-section {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: .75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .95rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 1.25rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .form-section-title i {
        color: var(--brand-secondary);
        font-size: 1rem;
    }

    .form-section .form-control,
    .form-section input.form-control,
    .form-section select.form-control,
    .form-section textarea.form-control {
        width: 100%;
        min-height: 46px;
        padding: 0.65rem 0.9rem;
        font-size: 0.95rem;
        line-height: 1.4;
        border: 1px solid #d1d5db;
        border-radius: 0.55rem;
        background: white;
        color: var(--brand-primary);
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-section textarea.form-control {
        min-height: 100px;
        resize: vertical;
    }

    .form-section .form-control:focus,
    .form-section select.form-control:focus,
    .form-section textarea.form-control:focus {
        outline: none;
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.12);
    }

    .form-section .form-control.is-invalid {
        border-color: #dc2626;
    }

    .form-section .form-control.is-invalid:focus {
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.12);
    }

    .form-section .form-control::placeholder {
        color: #9ca3af;
        font-size: 0.9rem;
    }

    .form-section select.form-control {
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23706f6c'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.9rem center;
        background-size: 14px 14px;
        padding-right: 2.75rem;
        cursor: pointer;
    }

    .form-section select.form-control:hover {
        border-color: #9ca3af;
    }

    .form-section select.form-control:focus {
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23374fa7'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
    }

    .form-section select.form-control option {
        padding: 0.5rem;
    }

    .input-group {
        display: flex;
        align-items: stretch;
        border-radius: 0.55rem;
        overflow: hidden;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
        background: white;
    }

    .input-group:focus-within {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.12);
    }

    .input-group-text {
        display: flex;
        align-items: center;
        padding: 0 0.9rem;
        background: #f9fafb;
        color: var(--brand-muted);
        font-weight: 700;
        font-size: 0.9rem;
        border: none;
        border-right: 1px solid #e5e7eb;
        flex-shrink: 0;
    }

    .input-group .form-control {
        border: none !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        min-height: 44px;
    }

    .file-upload-box {
        position: relative;
        border: 2px dashed #d1d5db;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        background: #fafafa;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .file-upload-box:hover {
        border-color: var(--brand-secondary);
        background: #f4f6fc;
    }

    .file-upload-box.dragover {
        border-color: var(--brand-secondary);
        background: #f4f6fc;
        transform: scale(1.005);
    }

    .file-upload-box input[type="file"] {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-icon {
        font-size: 2.5rem;
        color: var(--brand-secondary);
        margin-bottom: 0.5rem;
        display: block;
    }

    .file-upload-title {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--brand-primary);
        margin-bottom: 0.25rem;
    }

    .file-upload-subtitle {
        font-size: 0.8rem;
        color: var(--brand-muted);
    }

    .file-upload-subtitle strong {
        color: var(--brand-secondary);
    }

    .file-upload-files {
        margin-top: 0.75rem;
        font-size: 0.8rem;
        color: var(--brand-muted);
        display: flex;
        flex-wrap: wrap;
        gap: 0.35rem;
        justify-content: center;
    }

    .file-upload-files span {
        background: white;
        border: 1px solid #e5e7eb;
        padding: 0.2rem 0.6rem;
        border-radius: 9999px;
        font-weight: 500;
    }

    .form-section input[type="file"].form-control {
        padding: 0.5rem;
        min-height: 52px;
        background: #fafafa;
        cursor: pointer;
    }

    .form-section input[type="file"].form-control::file-selector-button {
        background: white;
        color: var(--brand-primary);
        border: 1px solid #d1d5db;
        border-radius: 0.4rem;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        margin-right: 0.75rem;
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-section input[type="file"].form-control::file-selector-button:hover {
        background: var(--brand-secondary);
        color: white;
        border-color: var(--brand-secondary);
    }

    .chip-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
        gap: 0.5rem;
    }

    .chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.65rem 0.9rem;
        border: 1.5px solid #e5e7eb;
        border-radius: 9999px;
        cursor: pointer;
        font-size: 0.85rem;
        background: white;
        transition: all 0.15s ease;
        user-select: none;
        font-weight: 500;
        color: var(--brand-primary);
    }

    .chip:hover {
        border-color: var(--brand-secondary);
        transform: translateY(-1px);
    }

    .chip input {
        accent-color: var(--brand-secondary);
        cursor: pointer;
    }

    .chip i {
        color: var(--brand-muted);
        font-size: 0.95rem;
    }

    .chip:has(input:checked) {
        background: #f4f6fc;
        border-color: var(--brand-secondary);
        color: var(--brand-secondary);
        font-weight: 700;
    }

    .chip:has(input:checked) i {
        color: var(--brand-secondary);
    }

    .toggle-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        cursor: pointer;
        padding-top: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--brand-primary);
    }

    .toggle-row input[type="checkbox"] { display: none; }

    .toggle-switch {
        width: 44px;
        height: 24px;
        background: #d1d5db;
        border-radius: 9999px;
        position: relative;
        transition: background 0.2s ease;
        flex-shrink: 0;
    }

    .toggle-switch::after {
        content: '';
        position: absolute;
        top: 3px;
        left: 3px;
        width: 18px;
        height: 18px;
        background: white;
        border-radius: 50%;
        transition: transform 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
    }

    .toggle-row input:checked + .toggle-switch {
        background: var(--brand-secondary);
    }

    .toggle-row input:checked + .toggle-switch::after {
        transform: translateX(20px);
    }

    .inline-number {
        display: inline-block;
        width: 76px;
        padding: 0.35rem 0.5rem !important;
        text-align: center;
        margin: 0 0.35rem;
        min-height: 38px !important;
        font-weight: 700;
        color: var(--brand-secondary);
    }

    .existing-images {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
        gap: .75rem;
        margin-bottom: 1rem;
    }

    .existing-image {
        position: relative;
        display: block;
        border-radius: .5rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        aspect-ratio: 1;
        cursor: grab;
        transition: all 0.2s ease;
        user-select: none;
    }

    .existing-image:active { cursor: grabbing; }

    .existing-image img,
    .existing-image video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        pointer-events: none;
    }

    .existing-image input[type=checkbox] {
        position: absolute;
        top: .5rem;
        left: .5rem;
        z-index: 2;
        width: 18px;
        height: 18px;
        accent-color: #dc2626;
        cursor: pointer;
    }

    .existing-image-overlay {
        position: absolute;
        inset: 0;
        background: rgba(220, 38, 38, .85);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .3rem;
        opacity: 0;
        transition: opacity .2s;
        font-weight: 600;
        font-size: 0.85rem;
        z-index: 1;
    }

    .existing-image:hover .existing-image-overlay,
    .existing-image:has(input:checked) .existing-image-overlay {
        opacity: 1;
    }

    .existing-image:has(input:checked) {
        opacity: .6;
    }

    .existing-image.dragging {
        opacity: 0.35;
        cursor: grabbing;
        transform: scale(0.95);
    }

    .existing-image.drag-over {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.3);
        transform: scale(1.04);
    }

    .existing-image-drag {
        position: absolute;
        top: 0.35rem;
        right: 0.35rem;
        width: 24px;
        height: 24px;
        border-radius: 4px;
        background: rgba(10, 10, 10, 0.7);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        z-index: 3;
        cursor: grab;
        opacity: 0;
        transition: opacity 0.15s ease;
        pointer-events: none;
    }

    .existing-image:hover .existing-image-drag {
        opacity: 1;
    }

    .existing-image-primary {
        position: absolute;
        bottom: .5rem;
        left: .5rem;
        background: var(--brand-secondary);
        color: #fff;
        font-size: .65rem;
        font-weight: 700;
        padding: .15rem .5rem;
        border-radius: 9999px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        z-index: 2;
    }

    .preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 0.6rem;
        margin-bottom: 0.85rem;
    }

    .preview-grid:empty { display: none; }

    .preview-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.55rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: #f3f4f6;
        cursor: grab;
        transition: all 0.15s ease;
        user-select: none;
    }

    .preview-item:hover { border-color: var(--brand-secondary); }

    .preview-item.dragging {
        opacity: 0.4;
        cursor: grabbing;
        transform: scale(0.97);
    }

    .preview-item.drag-over {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.25);
        transform: scale(1.03);
    }

    .preview-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
        display: block;
    }

    .preview-order {
        position: absolute;
        bottom: 0.35rem;
        left: 0.35rem;
        background: rgba(10, 10, 10, 0.75);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
        line-height: 1.2;
        pointer-events: none;
    }

    .preview-remove {
        position: absolute;
        top: 0.3rem;
        right: 0.3rem;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.95);
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .preview-remove:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }

    .video-preview-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.6rem;
        margin-bottom: 0.85rem;
    }

    .video-preview-grid:empty { display: none; }

    .video-preview-item {
        position: relative;
        aspect-ratio: 16/9;
        border-radius: 0.55rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: #000;
        cursor: grab;
        transition: all 0.15s ease;
        user-select: none;
    }

    .video-preview-item:hover { border-color: var(--brand-secondary); }

    .video-preview-item.dragging {
        opacity: 0.4;
        cursor: grabbing;
        transform: scale(0.97);
    }

    .video-preview-item.drag-over {
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.25);
        transform: scale(1.03);
    }

    .video-preview-item video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        pointer-events: none;
        display: block;
        background: #000;
    }

    .video-preview-order {
        position: absolute;
        bottom: 0.35rem;
        left: 0.35rem;
        background: rgba(10, 10, 10, 0.75);
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
        border-radius: 9999px;
        line-height: 1.2;
        pointer-events: none;
    }

    .video-preview-remove {
        position: absolute;
        top: 0.3rem;
        right: 0.3rem;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.95);
        color: #fff;
        border: none;
        cursor: pointer;
        font-size: 1rem;
        line-height: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
    }

    .video-preview-remove:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }

    .form-section .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--brand-primary);
        margin-bottom: 0.4rem;
    }

    .form-section .text-muted {
        display: block;
        font-size: 0.78rem;
        color: var(--brand-muted);
        margin-top: 0.3rem;
    }

    .form-section .invalid-feedback {
        font-size: 0.8rem;
        color: #dc2626;
        margin-top: 0.3rem;
        font-weight: 500;
    }

    .ms-wrapper { position: relative; }

    .ms-trigger {
        width: 100%;
        padding: 0.65rem 0.9rem;
        min-height: 46px;
        border: 1px solid #d1d5db;
        border-radius: 0.55rem;
        font-size: 0.95rem;
        font-family: inherit;
        color: var(--brand-primary);
        background: white;
        text-align: left;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        transition: all 0.15s ease;
    }

    .ms-trigger:hover { border-color: #9ca3af; }

    .ms-trigger:focus {
        outline: none;
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.12);
    }

    .ms-label {
        flex: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: var(--brand-primary);
        font-weight: 500;
    }

    .ms-label.placeholder { color: #9ca3af; }

    .ms-arrow {
        transition: transform 0.15s ease;
        font-size: 0.75rem;
        color: var(--brand-muted);
        flex-shrink: 0;
    }

    .ms-arrow.rotated { transform: rotate(180deg); }

    .ms-menu {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 0.55rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        z-index: 60;
        max-height: 260px;
        overflow-y: auto;
        padding: 0.35rem;
    }

    .ms-menu::-webkit-scrollbar { width: 6px; }
    .ms-menu::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

    .ms-item {
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding: 0.5rem 0.65rem;
        border-radius: 3px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: background 0.1s ease;
        user-select: none;
    }

    .ms-item:hover { background: #f9fafb; }
    .ms-item.checked { background: #f5f5f5; }

    .ms-item input[type="checkbox"] {
        accent-color: var(--brand-secondary);
        cursor: pointer;
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    input.date-input { cursor: pointer; background: white; }
    input.date-input[readonly] { cursor: not-allowed; background: var(--surface); opacity: 0.7; }

    .flatpickr-calendar {
        font-family: var(--font-sans);
        border-radius: 6px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        z-index: 100;
    }

    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
    }

    .flatpickr-day.today { border-color: var(--brand-primary); }
    .flatpickr-day:hover { background: var(--surface-alt); }
    .flatpickr-months .flatpickr-month { color: var(--brand-primary); }

    @media (max-width: 640px) {
        .form-section { padding: 1rem; }
        .chip-grid { grid-template-columns: 1fr 1fr; }
        .file-upload-box { padding: 1rem; }
        .file-upload-icon { font-size: 2rem; }
        .preview-grid { grid-template-columns: repeat(auto-fill, minmax(90px, 1fr)); }
        .video-preview-grid { grid-template-columns: 1fr; }
    }
</style>