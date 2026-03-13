@extends('layouts.app')

@section('title', $product->name . ' - Карточка товара')

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
                    @foreach($breadcrumbs as $breadcrumb)
                        @if($loop->last)
                            <li class="breadcrumb-item">
                                <a href="{{ route('catalog.category', $breadcrumb->id) }}">
                                    {{ $breadcrumb->name }}
                                </a>
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                <a href="{{ route('catalog.category', $breadcrumb->id) }}">
                                    {{ $breadcrumb->name }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $product->name }}
                    </li>
                </ol>
            </nav>

            {{-- Карточка товара --}}
            <div class="card">
                <div class="card-body">
                    <h1 class="card-title mb-4">{{ $product->name }}</h1>
                    
                    <div class="row">
                        <div class="col-md-6">
                            {{-- Изображение товара (заглушка) --}}
                            <div class="bg-light rounded d-flex align-items-center justify-content-center mb-4" 
                                 style="height: 300px;">
                                <i class="bi bi-image text-muted" style="font-size: 5rem;"></i>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            {{-- Информация о товаре --}}
                            <div class="mb-4">
                                @if($product->price)
                                    <p class="display-4 text-success fw-bold mb-3">
                                        {{ number_format($product->price->price, 2, '.', ' ') }} ₽
                                    </p>
                                @else
                                    <p class="text-muted mb-3">Цена не указана</p>
                                @endif
                            </div>
                            
                            <div class="mb-4">
                                <h5>
                                    <i class="bi bi-info-circle"></i> Информация
                                </h5>
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th scope="row" class="w-50">Артикул</th>
                                            <td>{{ $product->id }}</td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Категория</th>
                                            <td>
                                                @if($product->group)
                                                    <a href="{{ route('catalog.category', $product->group->id) }}">
                                                        {{ $product->group->name }}
                                                    </a>
                                                @else
                                                    Не указана
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Наличие</th>
                                            <td>
                                                <span class="badge bg-success">В наличии</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-primary btn-lg" type="button">
                                    <i class="bi bi-cart-plus"></i> Добавить в корзину
                                </button>
                                <a href="{{ route('catalog.category', $product->group?->id ?? 0) }}" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left"></i> Вернуться к категории
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
