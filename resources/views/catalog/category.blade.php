@extends('layouts.app')

@section('title', $group->name . ' - Каталог товаров')

@section('content')
    <div class="row">
        {{-- Сайдбар с группами --}}
        <div class="col-lg-3 mb-4">
            @include('catalog.partials.groups-sidebar')
        </div>

        {{-- Основной контент --}}
        <div class="col-lg-9">
            {{-- Хлебные крошки --}}
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('catalog.index') }}">
                            <i class="bi bi-house"></i> Главная
                        </a>
                    </li>
                    @php
                        $breadcrumbs = [];
                        $current = $group;
                        while ($current && $current->id) {
                            array_unshift($breadcrumbs, $current);
                            $current = $current->parent;
                        }
                    @endphp
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last)
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $breadcrumb->name }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ route('catalog.category', $breadcrumb->id) }}">
                                    {{ $breadcrumb->name }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>

            <h1 class="mb-3">
                <i class="bi bi-folder"></i> {{ $group->name }}
            </h1>

            @if($group->description)
                <p class="text-muted mb-4">{{ $group->description }}</p>
            @endif

            {{-- Подгруппы --}}
            @if($group->children->count() > 0)
                <div class="row mb-4">
                    @foreach($group->children as $child)
                        <div class="col-md-4 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="bi bi-folder2-open text-primary" style="font-size: 2rem;"></i>
                                    <h5 class="card-title mt-2">
                                        <a href="{{ route('catalog.category', $child->id) }}" class="text-decoration-none">
                                            {{ $child->name }}
                                        </a>
                                    </h5>
                                    <p class="products-count mb-0">
                                        Товаров: {{ $child->getTotalProductsCount() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Элементы управления --}}
            @include('catalog.partials.controls')

            {{-- Сетка товаров --}}
            <div id="products-container" class="row">
                @include('catalog.partials.products-grid')
            </div>

            {{-- Пагинация --}}
            <div id="pagination-container">
                @include('catalog.partials.pagination')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // AJAX для сортировки и пагинации
    function applySorting() {
        const sortField = document.getElementById('sort_field').value;
        const sortOrder = document.getElementById('sort_order').value;
        const perPage = document.getElementById('per_page').value;
        
        loadProducts(sortField, sortOrder, perPage);
    }

    function updatePerPage(value) {
        const sortField = document.getElementById('sort_field').value;
        const sortOrder = document.getElementById('sort_order').value;
        
        loadProducts(sortField, sortOrder, value);
    }

    function loadProducts(sort, order, perPage, page = null) {
        const url = new URL('{{ route("catalog.category.products.json", $group->id) }}');
        url.searchParams.set('sort', sort);
        url.searchParams.set('order', order);
        url.searchParams.set('per_page', perPage);
        
        if (page) {
            url.searchParams.set('page', page);
        }

        fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('products-container').innerHTML = data.html;
            document.getElementById('pagination-container').innerHTML = data.pagination;
            
            // Переназначаем обработчики для новой пагинации
            initPaginationHandlers();
        })
        .catch(error => console.error('Error:', error));
    }

    function initPaginationHandlers() {
        // Обработчики для пагинации
        document.querySelectorAll('#pagination-container a[href]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const url = new URL(this.href);
                const page = url.searchParams.get('page');
                const sort = url.searchParams.get('sort') || 'name';
                const order = url.searchParams.get('order') || 'asc';
                const perPage = url.searchParams.get('per_page') || 6;
                
                loadProducts(sort, order, perPage, page);
            });
        });
    }

    // Инициализация после загрузки страницы
    document.addEventListener('DOMContentLoaded', function() {
        initPaginationHandlers();
    });
</script>
@endpush
