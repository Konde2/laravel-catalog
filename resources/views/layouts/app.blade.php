<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Каталог товаров')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .product-card {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .product-card .card-body {
            display: flex;
            flex-direction: column;
        }
        
        .product-card .product-name {
            flex-grow: 1;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }
        
        .product-card .product-price {
            font-size: 1.25rem;
            font-weight: bold;
            color: #28a745;
        }
        
        .group-link {
            text-decoration: none;
            transition: background-color 0.2s;
        }
        
        .group-link:hover {
            background-color: #f8f9fa;
        }
        
        .breadcrumb-item a {
            text-decoration: none;
        }
        
        .sidebar {
            background-color: #f8f9fa;
            border-radius: 0.375rem;
            padding: 1rem;
        }
        
        .sidebar .list-group-item {
            border: none;
            padding: 0.5rem 1rem;
        }
        
        .sidebar .list-group-item.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
        
        .products-count {
            font-size: 0.8rem;
            color: #6c757d;
        }

        .sort-controls select {
            min-width: 150px;
        }

        .per-page-controls select {
            min-width: 80px;
        }

        /* Стили для подкатегорий в сайдбаре */
        .subcategories {
            border-left: 2px solid #dee2e6;
            margin-left: 18px;
        }

        .subcategories .list-group-item {
            font-size: 0.9rem;
            padding-left: 2.5rem !important;
        }

        .subcategories .subcategories {
            margin-left: 16px;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Навигация -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('catalog.index') }}">
                <i class="bi bi-shop"></i> Каталог
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('catalog.index') }}">Главная</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main class="py-4">
        <div class="container">
            @yield('content')
        </div>
    </main>

    <!-- Футер -->
    <footer class="bg-light py-4 mt-auto">
        <div class="container text-center">
            <p class="text-muted mb-0">&copy; {{ date('Y') }} Каталог товаров. Все права защищены.</p>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
