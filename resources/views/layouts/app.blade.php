<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JCM Realty Group') — JCM Realty Group</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('site.webmanifest') }}">
    <meta name="theme-color" content="#374fa7">


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400;1,9..40,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-primary: #0a0a0a;
            --brand-secondary: #374fa7;
            --brand-muted: #6b7280;
            --brand-light: #ffffff;
            --brand-dark: #0a0a0a;
            --border: #ebebeb;
            --border-strong: #d4d4d4;
            --surface: #fafafa;
            --surface-alt: #f5f5f5;
            --sidebar-width: 236px;
            --header-height: 64px;
            --font-sans: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-serif: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --font-display: 'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
            width: 100%;
        }

        body {
            font-family: var(--font-sans);
            font-feature-settings: 'ss01', 'cv11';
            -webkit-font-feature-settings: 'ss01', 'cv11';
            background: #ffffff;
            color: var(--brand-primary);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
            letter-spacing: -0.005em;
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }

        .brand-text {
            display: inline-flex;
            align-items: baseline;
            gap: 0.35rem;
            text-decoration: none;
            white-space: nowrap;
            line-height: 1;
        }

        .brand-mark {
            font-family: var(--font-display);
            font-size: 1.35rem;
            font-weight: 700;
            color: #1e40af;
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .brand-name {
            font-family: var(--font-sans);
            font-size: 0.75rem;
            font-weight: 500;
            color: #1e3a8a;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            line-height: 1;
        }

        .sidebar .brand-mark {
            font-size: 1.4rem;
        }

        .sidebar .brand-name {
            font-size: 0.7rem;
        }

        .navbar-brand .brand-mark {
            font-size: 1.2rem;
        }

        .navbar-brand .brand-name {
            font-size: 0.65rem;
        }

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: var(--sidebar-width);
            max-width: 85vw;
            height: 100vh;
            background: #ffffff;
            border-right: 1px solid var(--border);
            padding: 1.5rem 1rem;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.25s ease;
            will-change: transform;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--border-strong);
        }

        body.sidebar-hidden .sidebar {
            transform: translateX(-100%);
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            padding: 0 2.5rem 0 0.5rem;
            margin-bottom: 2.5rem;
            position: relative;
        }

        .sidebar-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: var(--brand-muted);
            padding: 0 0.75rem;
            margin: 1.25rem 0 0.5rem;
        }

        .sidebar-close {
            display: flex;
            position: absolute;
            top: 0;
            right: 0;
            width: 32px;
            height: 32px;
            background: transparent;
            border: none;
            color: var(--brand-muted);
            font-size: 1.15rem;
            cursor: pointer;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .sidebar-close:hover {
            background: var(--surface);
            color: var(--brand-secondary);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.55rem 0.75rem;
            color: #4b5563;
            text-decoration: none;
            border-radius: 4px;
            transition: all 0.12s ease;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 2px;
            line-height: 1.4;
            letter-spacing: -0.005em;
        }

        .sidebar-link:hover {
            background: var(--surface);
            color: var(--brand-primary);
        }

        .sidebar-link.active {
            background: var(--surface-alt);
            color: var(--brand-primary);
            font-weight: 600;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 18px;
            text-align: center;
            color: var(--brand-muted);
            flex-shrink: 0;
        }

        .sidebar-link.active i,
        .sidebar-link:hover i {
            color: var(--brand-secondary);
        }

        .sidebar-group {
            margin-bottom: 2px;
        }

        .sidebar-group-toggle {
            width: 100%;
            background: none;
            border: none;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.85rem;
            text-align: left;
        }

        .sidebar-chevron {
            transition: transform 0.18s ease;
            font-size: 0.7rem;
            margin-left: auto;
        }

        .sidebar-group.open .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-group-children {
            display: none;
            padding-left: 0.75rem;
            margin-top: 2px;
            margin-left: 0.9rem;
            border-left: 1px solid var(--border);
        }

        .sidebar-group.open .sidebar-group-children {
            display: block;
        }

        .sidebar-child {
            font-size: 0.82rem;
            padding: 0.45rem 0.7rem;
            color: #6b7280;
        }

        .navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--header-height);
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            z-index: 999;
            gap: 0.75rem;
            transition: left 0.25s ease;
            min-width: 0;
        }

        body.sidebar-hidden .navbar,
        body.no-sidebar .navbar {
            left: 0;
        }

        .navbar-brand {
            display: none;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }

        body.sidebar-hidden .navbar-brand,
        body.no-sidebar .navbar-brand {
            display: inline-flex;
        }

        .navbar-links {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            flex-shrink: 0;
            margin-left: 0.5rem;
        }

        .navbar-link {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.75rem;
            color: var(--brand-muted);
            text-decoration: none;
            font-size: 0.83rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.15s ease;
            white-space: nowrap;
        }

        .navbar-link:hover {
            background: var(--surface);
            color: var(--brand-primary);
            text-decoration: none;
        }

        .navbar-link.active {
            background: var(--surface-alt);
            color: var(--brand-primary);
            font-weight: 600;
        }

        .navbar-link i {
            font-size: 0.95rem;
        }

        .navbar-dropdown {
            position: relative;
            display: inline-flex;
        }

        .navbar-dropdown-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.45rem 0.75rem;
            color: var(--brand-muted);
            background: transparent;
            border: none;
            cursor: pointer;
            font-size: 0.83rem;
            font-weight: 500;
            border-radius: 4px;
            transition: all 0.15s ease;
            font-family: inherit;
            white-space: nowrap;
        }

        .navbar-dropdown-toggle:hover {
            background: var(--surface);
            color: var(--brand-primary);
        }

        .navbar-dropdown-toggle.active {
            background: var(--surface-alt);
            color: var(--brand-primary);
            font-weight: 600;
        }

        .navbar-dropdown-arrow {
            font-size: 0.65rem;
            transition: transform 0.15s ease;
        }

        .navbar-dropdown.open .navbar-dropdown-arrow {
            transform: rotate(180deg);
        }

        .navbar-dropdown-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 220px;
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            padding: 0.35rem;
            z-index: 2000;
            display: none;
            flex-direction: column;
            gap: 2px;
        }

        .navbar-dropdown.open .navbar-dropdown-menu {
            display: flex;
        }

        .navbar-dropdown-item {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.55rem 0.75rem;
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--brand-primary);
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.12s ease;
            white-space: nowrap;
        }

        .navbar-dropdown-item:hover {
            background: var(--surface);
            color: var(--brand-primary);
            text-decoration: none;
        }

        .navbar-dropdown-item.active {
            background: var(--surface-alt);
            font-weight: 600;
        }

        .navbar-dropdown-item i {
            color: var(--brand-muted);
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .navbar-dropdown-item.active i,
        .navbar-dropdown-item:hover i {
            color: var(--brand-secondary);
        }

        .navbar-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-left: auto;
            flex-shrink: 0;
        }

        .navbar-actions .btn-icon {
            background: none;
            border: none;
            font-size: 1.1rem;
            color: var(--brand-primary);
            cursor: pointer;
            padding: 0.4rem;
            transition: color 0.15s ease;
            position: relative;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .navbar-actions .btn-icon:hover {
            color: var(--brand-secondary);
        }

        .user-dropdown {
            position: absolute;
            right: 0;
            top: calc(100% + 0.5rem);
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            min-width: 220px;
            overflow: hidden;
            z-index: 2000;
        }

        .user-dropdown-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
        }

        .user-dropdown-header strong {
            font-size: 0.85rem;
            font-weight: 600;
            letter-spacing: -0.005em;
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
            font-size: 0.83rem;
            color: var(--brand-primary);
            text-decoration: none;
            transition: background 0.12s ease;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: inherit;
        }

        .user-dropdown-item:hover {
            background: var(--surface);
            color: var(--brand-primary);
            text-decoration: none;
        }

        .user-dropdown-item i {
            color: var(--brand-muted);
            font-size: 0.95rem;
        }

        .user-dropdown-logout {
            color: #b91c1c;
            border-top: 1px solid var(--border);
        }

        .user-dropdown-logout:hover {
            background: #fef2f2;
            color: #991b1b;
        }

        .user-dropdown-logout i {
            color: #b91c1c;
        }

        .menu-toggle {
            display: flex;
            background: none;
            border: none;
            font-size: 1.35rem;
            color: var(--brand-primary);
            cursor: pointer;
            padding: 0.35rem;
            flex-shrink: 0;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            transition: all 0.15s ease;
        }

        .menu-toggle:hover {
            background: var(--surface);
            color: var(--brand-secondary);
        }

        body.no-sidebar .menu-toggle {
            display: none;
        }

        @media (min-width: 768px) {
            body:not(.sidebar-hidden):not(.no-sidebar) .menu-toggle {
                display: none;
            }
        }

        .sidebar-overlay {
            display: none;
            pointer-events: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.4);
            z-index: 999;
            transition: opacity 0.2s ease;
        }

        body.sidebar-mobile-open .sidebar-overlay {
            display: block;
            pointer-events: auto;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: calc(var(--header-height) + 2rem) 2rem 2rem 2rem;
            min-height: 100vh;
            transition: margin-left 0.25s ease;
            width: calc(100% - var(--sidebar-width));
            max-width: calc(100% - var(--sidebar-width));
            overflow-x: hidden;
        }

        body.sidebar-hidden .main-content,
        body.no-sidebar .main-content {
            margin-left: 0;
            width: 100%;
            max-width: 100%;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border);
        }

        .page-title {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.025em;
            color: var(--brand-primary);
            line-height: 1.15;
        }

        .page-title span {
            color: var(--brand-secondary);
            font-weight: 700;
        }

        .btn-create {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.55rem 0.95rem;
            background: var(--brand-primary);
            color: white;
            border: 1px solid var(--brand-primary);
            border-radius: 4px;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.82rem;
            transition: all 0.15s ease;
            cursor: pointer;
            font-family: inherit;
            letter-spacing: 0.005em;
            white-space: nowrap;
        }

        .btn-create:hover {
            background: var(--brand-secondary);
            border-color: var(--brand-secondary);
            color: white;
            text-decoration: none;
        }

        .btn-edit {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.7rem;
            background: transparent;
            color: var(--brand-primary);
            border: 1px solid var(--border-strong);
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-edit:hover {
            background: var(--brand-primary);
            color: white;
            border-color: var(--brand-primary);
            text-decoration: none;
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.4rem 0.7rem;
            background: transparent;
            color: #b91c1c;
            border: 1px solid #fecaca;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            transition: all 0.15s ease;
            cursor: pointer;
            font-family: inherit;
            white-space: nowrap;
        }

        .btn-delete:hover {
            background: #b91c1c;
            color: white;
            border-color: #b91c1c;
        }

        .btn-icon-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            padding: 0;
            background: transparent;
            color: var(--brand-primary);
            border: 1px solid var(--border-strong);
            border-radius: 4px;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.15s ease;
            cursor: pointer;
            font-family: inherit;
            flex-shrink: 0;
        }

        .btn-icon-action:hover {
            background: var(--brand-primary);
            color: white;
            border-color: var(--brand-primary);
            text-decoration: none;
        }

        .btn-icon-action.danger {
            color: #b91c1c;
            border-color: #fecaca;
        }

        .btn-icon-action.danger:hover {
            background: #b91c1c;
            color: white;
            border-color: #b91c1c;
        }

        .card-custom {
            background: white;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            max-width: 100%;
        }

        .card-header-inner {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1rem 1.25rem;
            background: white;
            border-bottom: 1px solid var(--border);
            min-width: 0;
        }

        .card-header-inner i {
            color: var(--brand-muted);
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .card-header-inner span {
            font-size: 0.82rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            min-width: 0;
        }

        .badge-count {
            background: var(--surface-alt);
            color: var(--brand-primary);
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.15rem 0.55rem;
            border-radius: 9999px;
            margin-left: auto;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 0.2rem 0.7rem;
            border-radius: 9999px;
            font-size: 0.72rem;
            font-weight: 600;
            letter-spacing: 0.01em;
            white-space: nowrap;
            line-height: 1.4;
        }

        .status-badge.active {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.success {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.info {
            background: #dbeafe;
            color: #1e40af;
        }

        .status-badge.neutral {
            background: #f3f4f6;
            color: #4b5563;
        }

        .status-badge.accent {
            background: #fce7f3;
            color: #9d174d;
        }

        .status-badge.role {
            background: #e0e7ff;
            color: #3730a3;
        }

        .status-badge.superadmin {
            background: #fef3c7;
            color: #92400e;
        }

        .table {
            width: 100%;
            max-width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 0.75rem 1rem;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--brand-muted);
            border-bottom: 1px solid var(--border);
            background: white;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            font-size: 0.82rem;
            font-weight: 400;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .table tr:hover td {
            background: var(--surface);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            min-width: 0;
            overflow: hidden;
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--surface-alt);
            color: var(--brand-primary);
            border: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.7rem;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 500;
            font-size: 0.85rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        .user-email {
            color: var(--brand-muted);
            font-size: 0.78rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: block;
        }

        .actions-cell {
            display: flex;
            gap: 0.3rem;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: nowrap;
        }

        .text-end {
            text-align: right;
        }

        .text-muted {
            color: var(--brand-muted);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
        }

        .empty-state i {
            font-size: 2.5rem;
            color: #d1d5db;
            display: block;
            margin-bottom: 1rem;
        }

        .empty-state p {
            font-size: 0.85rem;
            color: var(--brand-muted);
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 500;
            font-size: 0.78rem;
            margin-bottom: 0.4rem;
            color: var(--brand-primary);
            letter-spacing: 0.01em;
        }

        .form-control {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid var(--border-strong);
            border-radius: 4px;
            font-size: 0.85rem;
            transition: all 0.15s ease;
            background: white;
            font-family: inherit;
            color: var(--brand-primary);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--brand-primary);
        }

        .form-control.is-invalid {
            border-color: #dc2626;
        }

        select.form-control {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 12px 12px;
            padding-right: 2.25rem;
            cursor: pointer;
        }

        select.form-control:hover {
            border-color: #9ca3af;
        }

        .invalid-feedback {
            color: #dc2626;
            font-size: 0.78rem;
            margin-top: 0.3rem;
        }

        .row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 1rem;
        }

        .page-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 99999;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            pointer-events: none;
        }

        .page-loader.active {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }

        .page-loader-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.85rem;
        }

        .page-loader-spinner {
            width: 32px;
            height: 32px;
            border: 2px solid var(--border);
            border-top-color: var(--brand-secondary);
            border-radius: 50%;
            animation: pageLoaderSpin 0.7s linear infinite;
        }

        @keyframes pageLoaderSpin {
            to {
                transform: rotate(360deg);
            }
        }

        .page-loader-content p {
            font-size: 0.75rem;
            color: var(--brand-muted);
            font-weight: 500;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            margin: 0;
        }

        .alert-stack {
            position: fixed;
            top: calc(var(--header-height) + 1rem);
            right: 1.5rem;
            z-index: 100000;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            max-width: 380px;
            width: calc(100% - 3rem);
            pointer-events: none;
        }

        .alert-toast {
            pointer-events: auto;
            background: white;
            border: 1px solid var(--border);
            border-left: 3px solid;
            border-radius: 8px;
            padding: 0.85rem 2.25rem 0.85rem 1rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: flex-start;
            gap: 0.7rem;
            font-size: 0.85rem;
            animation: alertSlideIn 0.25s ease;
            position: relative;
            min-width: 0;
        }

        .alert-toast.closing {
            animation: alertSlideOut 0.25s ease forwards;
        }

        .alert-toast.success {
            border-left-color: #16a34a;
        }

        .alert-toast.error {
            border-left-color: #dc2626;
        }

        .alert-toast.warning {
            border-left-color: #f59e0b;
        }

        .alert-toast.info {
            border-left-color: var(--brand-secondary);
        }

        .alert-toast .alert-icon {
            font-size: 1.1rem;
            flex-shrink: 0;
            margin-top: 0.05rem;
        }

        .alert-toast.success .alert-icon {
            color: #16a34a;
        }

        .alert-toast.error .alert-icon {
            color: #dc2626;
        }

        .alert-toast.warning .alert-icon {
            color: #f59e0b;
        }

        .alert-toast.info .alert-icon {
            color: var(--brand-secondary);
        }

        .alert-toast .alert-body {
            flex: 1;
            min-width: 0;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .alert-toast .alert-title {
            font-weight: 700;
            font-size: 0.85rem;
            color: var(--brand-primary);
            margin-bottom: 0.15rem;
            line-height: 1.3;
        }

        .alert-toast .alert-message {
            color: var(--brand-muted);
            font-size: 0.8rem;
            line-height: 1.45;
        }

        .alert-toast .alert-close {
            position: absolute;
            top: 0.55rem;
            right: 0.55rem;
            background: none;
            border: none;
            color: var(--brand-muted);
            cursor: pointer;
            padding: 0.2rem;
            font-size: 0.9rem;
            line-height: 1;
            border-radius: 3px;
            transition: all 0.15s;
        }

        .alert-toast .alert-close:hover {
            background: var(--surface);
            color: var(--brand-primary);
        }

        @keyframes alertSlideIn {
            from {
                opacity: 0;
                transform: translateX(24px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes alertSlideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(24px);
            }
        }

        @media (max-width: 1280px) {
            .main-content {
                padding: calc(var(--header-height) + 1.75rem) 1.5rem 1.75rem 1.5rem;
            }

            .navbar {
                padding: 0 1.25rem;
            }
        }

        @media (max-width: 1024px) {
            .main-content {
                padding: calc(var(--header-height) + 1.5rem) 1.25rem 1.5rem 1.25rem;
            }

            .navbar {
                padding: 0 1rem;
                gap: 0.6rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .table th {
                padding: 0.65rem 0.65rem;
                font-size: 0.65rem;
            }

            .table td {
                padding: 0.7rem 0.65rem;
                font-size: 0.78rem;
            }

            .actions-cell {
                gap: 0.25rem;
            }

            .btn-edit,
            .btn-delete {
                padding: 0.35rem 0.55rem;
                font-size: 0.72rem;
            }

            .btn-edit i,
            .btn-delete i {
                font-size: 0.72rem;
            }

            .btn-icon-action {
                width: 30px;
                height: 30px;
                font-size: 0.8rem;
            }

            .navbar-link span {
                display: none;
            }

            .navbar-link {
                padding: 0.45rem 0.6rem;
            }

            .navbar-dropdown-toggle span {
                display: none;
            }

            .navbar-dropdown-toggle {
                padding: 0.45rem 0.6rem;
            }

            .navbar-dropdown-menu {
                left: auto;
                right: 0;
            }
        }

        @media (max-width: 767px) {
            :root {
                --header-height: 58px;
            }

            .sidebar {
                width: 260px;
                max-width: 82vw;
                transform: translateX(-100%);
            }

            body.sidebar-mobile-open .sidebar {
                transform: translateX(0);
            }

            body.sidebar-hidden .sidebar {
                transform: translateX(-100%);
            }

            .sidebar-brand {
                padding-right: 2.5rem;
            }

            .navbar {
                left: 0;
                padding: 0 0.75rem;
                gap: 0.5rem;
            }

            .navbar-brand {
                display: inline-flex;
            }

            body.sidebar-mobile-open .navbar-brand {
                display: none;
            }

            body.sidebar-mobile-open .menu-toggle {
                display: none;
            }

            .main-content,
            body.no-sidebar .main-content,
            body.sidebar-hidden .main-content {
                margin-left: 0;
                width: 100%;
                max-width: 100%;
                padding: calc(var(--header-height) + 1rem) 0.85rem 1.25rem 0.85rem;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
                margin-bottom: 1.25rem;
                padding-bottom: 0.85rem;
            }

            .page-title {
                font-size: 1.3rem;
            }

            .card-header-inner {
                padding: 0.85rem 1rem;
            }

            .card-header-inner span {
                font-size: 0.78rem;
            }

            .table th {
                padding: 0.55rem 0.4rem;
                font-size: 0.6rem;
                letter-spacing: 0.04em;
            }

            .table td {
                padding: 0.55rem 0.4rem;
                font-size: 0.72rem;
            }

            .user-email {
                display: none;
            }

            .user-name {
                font-size: 0.78rem;
            }

            .avatar {
                width: 26px;
                height: 26px;
                font-size: 0.65rem;
            }

            .actions-cell {
                gap: 0.2rem;
                flex-wrap: wrap;
                justify-content: flex-end;
            }

            .btn-edit,
            .btn-delete {
                padding: 0.3rem 0.45rem;
                font-size: 0.68rem;
                gap: 0.2rem;
            }

            .btn-edit span,
            .btn-delete span {
                display: none;
            }

            .btn-edit i,
            .btn-delete i {
                font-size: 0.78rem;
            }

            .btn-icon-action {
                width: 28px;
                height: 28px;
                font-size: 0.75rem;
            }

            .btn-create {
                font-size: 0.78rem;
                padding: 0.45rem 0.8rem;
                gap: 0.3rem;
            }

            .row {
                grid-template-columns: 1fr;
                gap: 0.85rem;
            }

            .form-group {
                margin-bottom: 1rem;
            }

            .user-dropdown {
                right: -0.5rem;
                min-width: 200px;
            }

            .navbar-dropdown-toggle {
                padding: 0.4rem 0.5rem;
                font-size: 0.78rem;
            }

            .navbar-dropdown-menu {
                min-width: 200px;
                right: 0;
                left: auto;
            }

            .alert-stack {
                left: 0.75rem;
                right: 0.75rem;
                top: calc(var(--header-height) + 0.75rem);
                max-width: none;
                width: auto;
            }
        }

        @media (max-width: 480px) {

            .main-content,
            body.no-sidebar .main-content,
            body.sidebar-hidden .main-content {
                padding-left: 0.65rem;
                padding-right: 0.65rem;
            }

            .navbar {
                padding: 0 0.5rem;
                gap: 0.4rem;
            }

            .navbar-actions {
                gap: 0.3rem;
            }

            .navbar-actions .btn-icon {
                font-size: 1rem;
                padding: 0.35rem;
            }

            .page-title {
                font-size: 1.15rem;
            }

            .table th {
                padding: 0.45rem 0.3rem;
                font-size: 0.55rem;
            }

            .table td {
                padding: 0.5rem 0.3rem;
                font-size: 0.68rem;
            }

            .avatar {
                width: 22px;
                height: 22px;
                font-size: 0.6rem;
            }

            .user-name {
                font-size: 0.72rem;
            }

            .btn-edit,
            .btn-delete {
                padding: 0.25rem 0.35rem;
            }

            .btn-edit i,
            .btn-delete i {
                font-size: 0.72rem;
            }

            .btn-icon-action {
                width: 26px;
                height: 26px;
                font-size: 0.7rem;
            }

            .card-header-inner {
                padding: 0.75rem 0.85rem;
            }

            .user-dropdown {
                right: -0.75rem;
                min-width: 180px;
            }
        }

        @media (max-width: 360px) {
            .navbar-brand .brand-mark {
                font-size: 1rem;
            }

            .navbar-brand .brand-name {
                font-size: 0.55rem;
            }

            .table th {
                font-size: 0.5rem;
            }

            .table td {
                font-size: 0.62rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="@unless(auth()->check() && auth()->user()->canSeeSidebar()) no-sidebar @endunless">

    @php $showSidebar = auth()->check() && auth()->user()->canSeeSidebar(); @endphp

    <div class="page-loader" id="pageLoader" aria-hidden="true">
        <div class="page-loader-content">
            <div class="page-loader-spinner"></div>
            <p>Loading</p>
        </div>
    </div>

    <div class="alert-stack" id="alertStack" aria-live="polite" aria-atomic="false"></div>

    @if($showSidebar)
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeMobileSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <a href="{{ url('/') }}" style="text-decoration:none;">
                <span class="brand-text">
                    <span class="brand-mark">JCM</span>
                    <span class="brand-name">Realty Group</span>
                </span>
            </a>
            <button type="button"
                class="sidebar-close"
                onclick="toggleSidebar()"
                aria-label="Toggle sidebar">
                <i class="bi bi-list"></i>
            </button>
        </div>

        <div class="sidebar-section-label">Navigation</div>

        @foreach($navigationModules ?? [] as $module)
        @if($module->children->isEmpty())
        <a class="sidebar-link {{ $module->isActive() ? 'active' : '' }}"
            href="{{ $module->url }}">
            <i class="bi {{ $module->icon }}"></i>
            {{ $module->name }}
        </a>
        @else
        <div class="sidebar-group">
            <button type="button"
                class="sidebar-link sidebar-group-toggle {{ $module->isActive() || $module->children->contains(fn($c) => $c->isActive()) ? 'active' : '' }}"
                onclick="toggleSidebarGroup(this)">
                <i class="bi {{ $module->icon }}"></i>
                <span style="flex:1; text-align:left; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $module->name }}</span>
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
    @endif

    <nav class="navbar" id="navbar">
        @if($showSidebar)
        <button class="menu-toggle"
            onclick="toggleSidebar()"
            aria-label="Open menu">
            <i class="bi bi-list"></i>
        </button>
        @endif

        <a href="{{ url('/') }}" class="navbar-brand">
            <span class="brand-text">
                <span class="brand-mark">JCM</span>
                <span class="brand-name">Realty Group</span>
            </span>
        </a>

        @if(
        (isset($publicModules) && $publicModules->isNotEmpty()) ||
        (isset($navbarModules) && $navbarModules->isNotEmpty())
        )
        <nav class="navbar-links" aria-label="Main navigation">
            @if(isset($publicModules))
            @foreach($publicModules as $module)
            @if($module->children->isEmpty())
            <a href="{{ $module->url }}"
                class="navbar-link {{ $module->isActive() ? 'active' : '' }}">
                <i class="bi {{ $module->icon }}"></i>
                <span>{{ $module->name }}</span>
            </a>
            @else
            <div class="navbar-dropdown" data-nav-dropdown>
                <button type="button"
                    class="navbar-link navbar-dropdown-toggle {{ $module->isActive() || $module->children->contains(fn($c) => $c->isActive()) ? 'active' : '' }}"
                    data-nav-dropdown-toggle>
                    <i class="bi {{ $module->icon }}"></i>
                    <span>{{ $module->name }}</span>
                    <i class="bi bi-chevron-down navbar-dropdown-arrow"></i>
                </button>
                <div class="navbar-dropdown-menu" data-nav-dropdown-menu>
                    @foreach($module->children as $child)
                    <a href="{{ $child->url }}"
                        class="navbar-dropdown-item {{ $child->isActive() ? 'active' : '' }}">
                        <i class="bi {{ $child->icon }}"></i>
                        <span>{{ $child->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            @endif

            @if(isset($navbarModules))
            @foreach($navbarModules as $module)
            @if($module->children->isEmpty())
            <a href="{{ $module->url }}"
                class="navbar-link {{ $module->isActive() ? 'active' : '' }}">
                <i class="bi {{ $module->icon }}"></i>
                <span>{{ $module->name }}</span>
            </a>
            @else
            <div class="navbar-dropdown" data-nav-dropdown>
                <button type="button"
                    class="navbar-link navbar-dropdown-toggle {{ $module->isActive() || $module->children->contains(fn($c) => $c->isActive()) ? 'active' : '' }}"
                    data-nav-dropdown-toggle>
                    <i class="bi {{ $module->icon }}"></i>
                    <span>{{ $module->name }}</span>
                    <i class="bi bi-chevron-down navbar-dropdown-arrow"></i>
                </button>
                <div class="navbar-dropdown-menu" data-nav-dropdown-menu>
                    @foreach($module->children as $child)
                    <a href="{{ $child->url }}"
                        class="navbar-dropdown-item {{ $child->isActive() ? 'active' : '' }}">
                        <i class="bi {{ $child->icon }}"></i>
                        <span>{{ $child->name }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach
            @endif
        </nav>
        @endif

        <div class="navbar-actions">
            @include('partials.translate')

            @auth
            <div class="user-menu" style="position: relative;">
                <button class="btn-icon" id="userMenuToggle" title="{{ auth()->user()->name }}" aria-label="User menu">
                    <i class="bi bi-person"></i>
                </button>
                <div class="user-dropdown" id="userDropdown" style="display: none;">
                    <div class="user-dropdown-header">
                        <strong>{{ auth()->user()->name }}</strong>
                        <small>{{ auth()->user()->email }}</small>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                        <i class="bi bi-person-gear"></i> My profile
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="user-dropdown-item user-dropdown-logout">
                            <i class="bi bi-box-arrow-right"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
            @else
            <a href="{{ route('login') }}" class="btn-create" style="font-size: 0.78rem; padding: 0.45rem 0.8rem;">
                Sign in
            </a>
            @endauth
        </div>
    </nav>

    <main class="main-content">
        @yield('content')
    </main>

    <script>
        window.sanitizeErrorMessage = function(msg) {
            if (!msg) return 'Something went wrong. Please try again.';
            const s = String(msg);
            if (/SQLSTATE\[\w+\]|PDOException|QueryException|Undefined (variable|property|index|method)|Call to (undefined|a member function)|Fatal error|Parse error|Stack trace|vendor[\/\\]laravel|->getMessage\(\)|#\d+\s+\/var\/www\/html|column reference|constraint .* violated|relation .* does not exist|Class .* does not exist|Target class/i.test(s)) {
                return 'We could not complete this action. Please verify your information and try again.';
            }
            return s;
        };

        window.showAlert = function(type, title, message, duration) {
            const stack = document.getElementById('alertStack');
            if (!stack) return;

            const safeType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
            const iconMap = {
                success: 'bi-check-circle-fill',
                error: 'bi-exclamation-circle-fill',
                warning: 'bi-exclamation-triangle-fill',
                info: 'bi-info-circle-fill',
            };

            const toast = document.createElement('div');
            toast.className = 'alert-toast ' + safeType;

            const closeBtn = document.createElement('button');
            closeBtn.type = 'button';
            closeBtn.className = 'alert-close';
            closeBtn.setAttribute('aria-label', 'Close alert');
            closeBtn.innerHTML = '<i class="bi bi-x-lg"></i>';

            const icon = document.createElement('i');
            icon.className = 'bi ' + (iconMap[safeType] || iconMap.info) + ' alert-icon';

            const body = document.createElement('div');
            body.className = 'alert-body';

            if (title) {
                const titleEl = document.createElement('div');
                titleEl.className = 'alert-title';
                titleEl.textContent = String(title);
                body.appendChild(titleEl);
            }

            const messageEl = document.createElement('div');
            messageEl.className = 'alert-message';
            messageEl.textContent = String(message || '');
            body.appendChild(messageEl);

            toast.appendChild(icon);
            toast.appendChild(body);
            toast.appendChild(closeBtn);
            stack.appendChild(toast);

            let closed = false;
            const close = () => {
                if (closed) return;
                closed = true;
                toast.classList.add('closing');
                setTimeout(() => toast.remove(), 260);
            };

            closeBtn.addEventListener('click', close);
            if (duration !== 0) {
                setTimeout(close, duration || 6000);
            }

            return {
                close
            };
        };
        const SIDEBAR_STORAGE_KEY = 'rentsky.sidebarHidden';

        function isMobileViewport() {
            return window.innerWidth < 768;
        }

        function toggleSidebar() {
            if (isMobileViewport()) {
                const isOpen = document.body.classList.contains('sidebar-mobile-open');
                if (isOpen) {
                    closeMobileSidebar();
                } else {
                    document.body.classList.add('sidebar-mobile-open');
                }
            } else {
                const isHidden = document.body.classList.contains('sidebar-hidden');
                if (isHidden) {
                    document.body.classList.remove('sidebar-hidden');
                    try {
                        localStorage.setItem(SIDEBAR_STORAGE_KEY, '0');
                    } catch (e) {}
                } else {
                    document.body.classList.add('sidebar-hidden');
                    try {
                        localStorage.setItem(SIDEBAR_STORAGE_KEY, '1');
                    } catch (e) {}
                }
            }
        }

        function closeMobileSidebar() {
            document.body.classList.remove('sidebar-mobile-open');
        }

        function initSidebarState() {
            if (isMobileViewport()) {
                document.body.classList.remove('sidebar-hidden');
                document.body.classList.remove('sidebar-mobile-open');
            } else {
                document.body.classList.remove('sidebar-mobile-open');
                let hidden = false;
                try {
                    hidden = localStorage.getItem(SIDEBAR_STORAGE_KEY) === '1';
                } catch (e) {}
                if (hidden) {
                    document.body.classList.add('sidebar-hidden');
                } else {
                    document.body.classList.remove('sidebar-hidden');
                }
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSidebarState();
            @if(session('success'))
            window.showAlert('success', 'Success', window.sanitizeErrorMessage(@json(session('success'))));
            @endif
            @if(session('error'))
            window.showAlert('error', 'Something went wrong', window.sanitizeErrorMessage(@json(session('error'))));
            @endif
            @if(session('warning'))
            window.showAlert('warning', 'Notice', window.sanitizeErrorMessage(@json(session('warning'))));
            @endif
            @if(session('info'))
            window.showAlert('info', 'Info', window.sanitizeErrorMessage(@json(session('info'))));
            @endif

            document.querySelectorAll('.sidebar-group').forEach(group => {
                if (group.querySelector('.sidebar-child.active')) {
                    group.classList.add('open');
                }
            });

            document.querySelectorAll('.sidebar a.sidebar-link, .sidebar .sidebar-group-children a').forEach(link => {
                link.addEventListener('click', function() {
                    if (isMobileViewport()) {
                        closeMobileSidebar();
                    }
                });
            });
        });

        let lastMobileState = isMobileViewport();
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                const nowMobile = isMobileViewport();
                if (nowMobile !== lastMobileState) {
                    lastMobileState = nowMobile;
                    initSidebarState();
                }
            }, 120);
        });

        function toggleSidebarGroup(btn) {
            const group = btn.closest('.sidebar-group');
            if (group) group.classList.toggle('open');
        }

        (function() {
            const dropdowns = document.querySelectorAll('[data-nav-dropdown]');
            if (!dropdowns.length) return;

            dropdowns.forEach(function(dropdown) {
                const toggle = dropdown.querySelector('[data-nav-dropdown-toggle]');
                const menu = dropdown.querySelector('[data-nav-dropdown-menu]');
                if (!toggle || !menu) return;

                toggle.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const isOpen = dropdown.classList.contains('open');
                    document.querySelectorAll('[data-nav-dropdown].open').forEach(d => {
                        d.classList.remove('open');
                    });
                    if (!isOpen) dropdown.classList.add('open');
                });
            });

            document.addEventListener('click', function(e) {
                if (!e.target.closest('[data-nav-dropdown]')) {
                    document.querySelectorAll('[data-nav-dropdown].open').forEach(d => {
                        d.classList.remove('open');
                    });
                }
            });

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('[data-nav-dropdown].open').forEach(d => {
                        d.classList.remove('open');
                    });
                }
            });
        })();

        (function() {
            const toggle = document.getElementById('userMenuToggle');
            const dropdown = document.getElementById('userDropdown');
            if (!toggle || !dropdown) return;

            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && e.target !== toggle) {
                    dropdown.style.display = 'none';
                }
            });
        })();

        (function() {
            const loader = document.getElementById('pageLoader');
            if (!loader) return;
            let hideTimer;

            const DOWNLOAD_PATTERNS = [
                /\.pdf(\?|#|$)/i,
                /\/pdf(\/|\?|#|$)/i,
                /\/download(\/|\?|#|$)/i,
                /\/export(\/|\?|#|$)/i,
            ];

            function isDownloadHref(href) {
                if (!href) return false;
                for (let i = 0; i < DOWNLOAD_PATTERNS.length; i++) {
                    if (DOWNLOAD_PATTERNS[i].test(href)) return true;
                }
                return false;
            }

            function showLoader() {
                clearTimeout(hideTimer);
                loader.classList.add('active');
                hideTimer = setTimeout(hideLoader, 15000);
            }

            function hideLoader() {
                clearTimeout(hideTimer);
                loader.classList.remove('active');
            }

            document.addEventListener('submit', function(e) {
                const form = e.target;
                if (form.matches('form') && !form.dataset.noLoader) showLoader();
            }, true);

            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (!link) return;
                if (link.dataset.noLoader !== undefined) return;
                if (link.target === '_blank') return;
                if (link.hasAttribute('download')) return;

                const href = link.getAttribute('href');
                if (!href) return;
                if (href.startsWith('#')) return;
                if (href.startsWith('mailto:') || href.startsWith('tel:')) return;
                if (href.startsWith('http') && !href.startsWith(window.location.origin)) return;
                if (href.startsWith('javascript:')) return;
                if (isDownloadHref(href)) return;

                showLoader();
            }, true);

            window.addEventListener('beforeunload', showLoader);
            window.addEventListener('pageshow', hideLoader);

            window.RentSky = window.RentSky || {};
            window.RentSky.showLoader = showLoader;
            window.RentSky.hideLoader = hideLoader;
        })();
    </script>

    @stack('scripts')

    @include('partials.chatbot')
</body>

</html>
