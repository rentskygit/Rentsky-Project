<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'JCM Realty Group')) - {{ config('app.name') }}</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-primary: #1B1B18;
            --brand-secondary: #004e6f;
            --brand-muted: #706f6c;
            --brand-light: #FDFDFC;
            --brand-dark: #1b1b18;
            --sidebar-width: 250px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--brand-light);
            color: var(--brand-dark);
            display: flex;
            min-height: 100vh;
        }

        /* ── Brand (JCM Realty) ── */
        .brand-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--brand-primary);
            letter-spacing: -0.02em;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .brand-text span {
            color: var(--brand-secondary);
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: white;
            border-right: 1px solid #e5e7eb;
            padding: 2rem 1rem;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 0.75rem;
            margin-bottom: 2rem;
            position: relative;
        }

        /* ── Botón de cerrar sidebar (solo móvil) ── */
        .sidebar-close {
            display: none;
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 36px;
            height: 36px;
            background: #f3f4f6;
            border: none;
            border-radius: 50%;
            color: var(--brand-muted);
            font-size: 1rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 10;
        }

        .sidebar-close:hover {
            background: var(--brand-secondary);
            color: white;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--brand-muted);
            text-decoration: none;
            border-radius: 0.5rem;
            transition: all 0.2s ease;
            font-size: 0.95rem;
            margin-bottom: 0.25rem;
        }

        .sidebar-link:hover {
            background: #f3f4f6;
            color: var(--brand-primary);
        }

        .sidebar-link.active {
            background: var(--brand-secondary);
            color: white;
        }

        .sidebar-link i {
            font-size: 1.2rem;
            width: 1.5rem;
            text-align: center;
        }

        /* ── Sidebar acordeón (grupos con hijos) ── */
        .sidebar-group {
            margin-bottom: 0.25rem;
        }

        .sidebar-group-toggle {
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.95rem;
            text-align: left;
        }

        .sidebar-chevron {
            transition: transform 0.2s ease;
            font-size: 0.8rem;
        }

        .sidebar-group.open .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-group-children {
            display: none;
            padding-left: 0.75rem;
            margin-top: 0.25rem;
            border-left: 2px solid #f3f4f6;
            margin-left: 1.5rem;
        }

        .sidebar-group.open .sidebar-group-children {
            display: block;
        }

        .sidebar-child {
            font-size: 0.875rem;
            padding: 0.55rem 0.85rem;
        }

        /* ── Navbar ── */
        .navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            padding: 0 2rem;
            z-index: 999;
            gap: 1rem;
            transition: left 0.3s ease;
        }

        /* Navbar brand: oculto por defecto (desktop). Se muestra en móvil. */
        .navbar-brand {
            display: none;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        .navbar-brand .brand-text {
            font-size: 1.2rem;
        }

        .navbar-search {
            flex: 1;
            max-width: 500px;
            position: relative;
            margin: 0 auto;
        }

        .navbar-search input {
            width: 100%;
            padding: 0.5rem 1rem 0.5rem 2.5rem;
            border: 1px solid #d1d5db;
            border-radius: 9999px;
            font-size: 0.9rem;
            background: #f3f4f6;
            transition: all 0.2s ease;
            outline: none;
        }

        .navbar-search input:focus {
            background: white;
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1);
        }

        .navbar-search .search-icon {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-muted);
        }

        .navbar-search .search-clear {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--brand-muted);
            cursor: pointer;
            display: none;
            background: none;
            border: none;
            font-size: 1rem;
        }

        .navbar-search .search-clear.visible {
            display: block;
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .navbar-actions .btn-icon {
            background: none;
            border: none;
            font-size: 1.2rem;
            color: var(--brand-muted);
            cursor: pointer;
            padding: 0.25rem;
            transition: color 0.2s ease;
            position: relative;
        }

        .navbar-actions .btn-icon:hover {
            color: var(--brand-primary);
        }

        .navbar-actions .badge-count {
            position: absolute;
            top: -4px;
            right: -6px;
            background: var(--brand-secondary);
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.1rem 0.4rem;
            border-radius: 9999px;
            min-width: 16px;
            text-align: center;
        }

        /* ── User dropdown (navbar) ── */
        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.75rem);
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            min-width: 220px;
            overflow: hidden;
            z-index: 2000;
        }

        .user-dropdown-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .user-dropdown-header strong {
            font-size: 0.9rem;
            color: var(--brand-primary);
        }

        .user-dropdown-header small {
            font-size: 0.75rem;
            color: var(--brand-muted);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.65rem 1rem;
            font-size: 0.875rem;
            color: var(--brand-primary);
            text-decoration: none;
            transition: background 0.15s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .user-dropdown-item:hover {
            background: #f9fafb;
            color: var(--brand-primary);
            text-decoration: none;
        }

        .user-dropdown-logout {
            color: #dc2626;
            border-top: 1px solid #f3f4f6;
        }

        .user-dropdown-logout:hover {
            background: #fef2f2;
            color: #991b1b;
        }

        /* ── Menú Hamburguesa ── */
        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--brand-primary);
            cursor: pointer;
            padding: 0.25rem;
            flex-shrink: 0;
        }

        .menu-toggle:hover {
            color: var(--brand-secondary);
        }

        /* ── Overlay para cerrar sidebar en móvil ── */
        .sidebar-overlay {
            display: none;
            pointer-events: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 999;
        }

        .sidebar-overlay.active {
            display: block;
            pointer-events: auto;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            padding: calc(var(--header-height) + 2rem) 2rem 2rem 2rem;
            min-height: 100vh;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--brand-primary);
        }

        .page-title span {
            color: var(--brand-secondary);
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.25rem;
            background: var(--brand-secondary);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-create:hover {
            background: #d42b02;
            color: white;
            text-decoration: none;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 48, 3, 0.3);
        }

        .btn-edit {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            background: #e5e7eb;
            color: #374151;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-edit:hover {
            background: #d1d5db;
            color: #1f2937;
            text-decoration: none;
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.3rem 0.8rem;
            background: #fee2e2;
            color: #991b1b;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.8rem;
            font-weight: 500;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-delete:hover {
            background: #fecaca;
            color: #7f1d1d;
        }

        .card-custom {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        .card-header-inner {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
        }

        .badge-count {
            background: var(--brand-secondary);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            margin-left: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 0.75rem 1.5rem;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-muted);
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .table td {
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: middle;
        }

        .table tr:hover {
            background: #f9fafb;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .avatar {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background: var(--brand-secondary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 500;
        }

        .user-email {
            color: var(--brand-muted);
            font-size: 0.9rem;
        }

        .actions-cell {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .text-end {
            text-align: right;
        }

        .text-muted {
            color: var(--brand-muted);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--brand-primary);
        }

        .form-control {
            width: 100%;
            padding: 0.6rem 0.875rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: white;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-secondary);
            box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.85rem;
            margin-top: 0.25rem;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .navbar {
                padding: 0 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.active {
                display: block;
            }

            .sidebar-close {
                display: flex;
            }

            .sidebar-brand {
                padding-right: 3rem;
            }

            .navbar {
                left: 0;
                padding: 0 1rem;
                gap: 0.5rem;
            }

            .menu-toggle {
                display: block;
            }

            /* Mostrar brand del navbar SOLO en móvil */
            .navbar-brand {
                display: inline-flex;
            }

            /* Si el sidebar está abierto, ocultar el brand del navbar */
            body.sidebar-is-open .navbar-brand {
                display: none;
            }

            .navbar-search {
                max-width: none;
                margin: 0;
                flex: 1;
            }

            .navbar-search input {
                font-size: 0.85rem;
                padding: 0.4rem 0.75rem 0.4rem 2rem;
            }

            .main-content {
                margin-left: 0;
                padding: calc(var(--header-height) + 1rem) 1rem 1rem 1rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .table {
                font-size: 0.85rem;
            }

            .table th,
            .table td {
                padding: 0.5rem 0.75rem;
            }

            .user-cell .user-email {
                display: none;
            }

            .actions-cell {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .navbar {
                padding: 0 0.5rem;
            }

            .navbar-search input {
                font-size: 0.75rem;
                padding: 0.3rem 0.5rem 0.3rem 1.8rem;
            }

            .navbar-search .search-icon {
                font-size: 0.8rem;
                left: 0.5rem;
            }

            .navbar-actions .btn-icon {
                font-size: 1rem;
            }

            .navbar-actions .badge-count {
                font-size: 0.5rem;
                min-width: 12px;
                top: -2px;
                right: -4px;
            }

            .main-content {
                padding: calc(var(--header-height) + 0.5rem) 0.5rem 0.5rem 0.5rem;
            }

            .page-title {
                font-size: 1.2rem;
            }

            .btn-create {
                font-size: 0.8rem;
                padding: 0.4rem 0.8rem;
            }

            .table {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.3rem 0.5rem;
            }

            .avatar {
                width: 1.8rem;
                height: 1.8rem;
                font-size: 0.6rem;
            }

            .btn-edit,
            .btn-delete {
                font-size: 0.65rem;
                padding: 0.15rem 0.5rem;
            }
        }

        @media (max-width: 360px) {
            .navbar-actions {
                gap: 0.5rem;
            }

            .navbar-actions .btn-icon {
                font-size: 0.9rem;
            }

            .table th,
            .table td {
                padding: 0.2rem 0.3rem;
            }

            .actions-cell {
                gap: 0.2rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>

    {{-- ── Overlay para cerrar sidebar en móvil ── --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- ── Sidebar ── --}}
    <aside class="sidebar" id="sidebar">
        <button type="button"
                class="sidebar-close"
                onclick="toggleSidebar()"
                aria-label="Cerrar menú">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="sidebar-brand">
        <a href="/" class="sidebar-brand">
                <span class="brand-text">JCM<span>Realty Group</span></span>
            </a>
        </div>

        @foreach($navigationModules ?? [] as $module)
            @if($module->children->isEmpty())
                <a class="sidebar-link {{ $module->isActive() ? 'active' : '' }}"
                   href="{{ $module->url }}">
                    <i class="bi {{ $module->icon }}"></i>
                    {{ $module->name }}
                </a>
            @else
                {{-- Módulo con hijos: acordeón --}}
                <div class="sidebar-group">
                    <button type="button"
                            class="sidebar-link sidebar-group-toggle {{ $module->isActive() || $module->children->contains(fn($c) => $c->isActive()) ? 'active' : '' }}"
                            onclick="toggleSidebarGroup(this)">
                        <i class="bi {{ $module->icon }}"></i>
                        <span style="flex:1; text-align:left;">{{ $module->name }}</span>
                        <i class="bi bi-chevron-down sidebar-chevron"></i>
                    </button>
                    <div class="sidebar-group-children">
                        @foreach($module->children as $child)
                            <a class="sidebar-link sidebar-child {{ $child->isActive() ? 'active' : '' }}"
                               href="{{ $child->url }}">
                                <i class="bi {{ $child->icon }}"></i>
                                {{ $child->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </aside>

    {{-- ── Navbar ── --}}
    <nav class="navbar" id="navbar">
        <button class="menu-toggle"
                onclick="event.stopPropagation(); toggleSidebar();"
                aria-label="Abrir menú">
            <i class="bi bi-list"></i>
        </button>

        <a href="/" class="navbar-brand">
            <span class="brand-text">The<span>Outlet</span></span>
        </a>

        <div class="navbar-search">
            <i class="bi bi-search search-icon"></i>
            <input
                type="text"
                id="globalSearch"
                placeholder="Buscar productos, categorías, usuarios..."
                aria-label="Búsqueda global"
                autocomplete="off"
            >
            <button class="search-clear" id="searchClear" aria-label="Limpiar búsqueda">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>

        <div class="navbar-actions">
            <button class="btn-icon" title="Carrito" aria-label="Carrito">
                <i class="bi bi-cart3"></i>
                <span class="badge-count">3</span>
            </button>

            @auth
                <div class="user-menu" style="position: relative;">
                    <button class="btn-icon" id="userMenuToggle" title="{{ auth()->user()->name }}" aria-label="Usuario">
                        <i class="bi bi-person-circle"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown" style="display: none;">
                        <div class="user-dropdown-header">
                            <strong>{{ auth()->user()->name }}</strong>
                            <small>{{ auth()->user()->email }}</small>
                        </div>
                        <a href="{{ route('users.edit', auth()->user()) }}" class="user-dropdown-item">
                            <i class="bi bi-person-gear"></i> Mi perfil
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="user-dropdown-item user-dropdown-logout">
                                <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            @endauth
        </div>
    </nav>

    {{-- ── Main Content ── --}}
    <main class="main-content">
        @yield('content')
    </main>

    <script>
        // ── Sidebar toggle ──
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
            body.classList.toggle('sidebar-is-open', sidebar.classList.contains('open'));
        }

        // ── Cerrar sidebar al hacer clic fuera en móvil ──
        document.addEventListener('click', function (event) {
            if (window.innerWidth > 768) return;

            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.menu-toggle');
            const overlay = document.getElementById('sidebarOverlay');
            const closeBtn = document.querySelector('.sidebar-close');

            if (!sidebar || !toggle) return;

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedOnToggle = toggle.contains(event.target);
            const clickedOnOverlay = overlay && overlay.contains(event.target);
            const clickedOnClose = closeBtn && closeBtn.contains(event.target);

            // No cerrar si el click fue dentro del sidebar, en el toggle, en el overlay o en la X
            if (clickedOnClose) return;

            if (!clickedInsideSidebar && !clickedOnToggle && !clickedOnOverlay) {
                sidebar.classList.remove('open');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-is-open');
            }
        });

        // ── Cerrar sidebar al hacer click en un enlace (solo móvil) ──
        document.querySelectorAll('.sidebar a.sidebar-link, .sidebar .sidebar-group-children a').forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    const sidebar = document.getElementById('sidebar');
                    const overlay = document.getElementById('sidebarOverlay');
                    sidebar.classList.remove('open');
                    overlay.classList.remove('active');
                    document.body.classList.remove('sidebar-is-open');
                }
            });
        });

        // ── Search logic ──
        (function () {
            const searchInput = document.getElementById('globalSearch');
            const clearBtn = document.getElementById('searchClear');
            if (!searchInput || !clearBtn) return;

            function toggleClearButton() {
                if (searchInput.value.length > 0) {
                    clearBtn.classList.add('visible');
                } else {
                    clearBtn.classList.remove('visible');
                }
            }

            searchInput.addEventListener('input', toggleClearButton);
            clearBtn.addEventListener('click', function () {
                searchInput.value = '';
                toggleClearButton();
                searchInput.focus();
            });

            searchInput.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length > 0) {
                        window.location.href = '/search?q=' + encodeURIComponent(query);
                    }
                }
            });

            toggleClearButton();

            const urlParams = new URLSearchParams(window.location.search);
            const searchQuery = urlParams.get('q');
            if (searchQuery) {
                searchInput.value = searchQuery;
                toggleClearButton();
            }
        })();

        // ── Confirm delete ──
        function confirmDelete(event, name) {
            event.preventDefault();
            if (confirm(`¿Estás seguro de eliminar a "${name}"?`)) {
                event.target.closest('form').submit();
            }
            return false;
        }

        // ── User dropdown ──
        (function () {
            const toggle = document.getElementById('userMenuToggle');
            const dropdown = document.getElementById('userDropdown');
            if (!toggle || !dropdown) return;

            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && e.target !== toggle) {
                    dropdown.style.display = 'none';
                }
            });
        })();

        // ── Sidebar groups (acordeón) ──
        function toggleSidebarGroup(btn) {
            const group = btn.closest('.sidebar-group');
            if (group) {
                group.classList.toggle('open');
            }
        }

        // Auto-abrir los grupos que contienen un hijo activo (solo al cargar)
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sidebar-group').forEach(group => {
                if (group.querySelector('.sidebar-child.active')) {
                    group.classList.add('open');
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>