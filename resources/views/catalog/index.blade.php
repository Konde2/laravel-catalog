@extends('layouts.app')

@section('title', 'Каталог товаров')

@section('content')
    <div class="row">
        {{-- Сайдбар с группами --}}
        <div class="col-lg-3 mb-4">
            @php
                $currentGroupId = null;
                $parentGroupIds = [];
            @endphp
            @include('catalog.partials.groups-sidebar')
        </div>

        {{-- Основной контент с товарами --}}
        <div class="col-lg-9">
            <h1 class="mb-4">
                <i class="bi bi-shop"></i> Все товары
            </h1>

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
        const url = new URL('{{ route("catalog.products.json") }}');
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
