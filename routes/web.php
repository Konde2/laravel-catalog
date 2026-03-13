<?php

use App\Http\Controllers\CatalogController;
use Illuminate\Support\Facades\Route;

// Главная страница каталога
Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');

// Категория (группа товаров)
Route::get('/category/{groupId}', [CatalogController::class, 'category'])->name('catalog.category');

// Карточка товара
Route::get('/product/{productId}', [CatalogController::class, 'product'])->name('catalog.product');

// AJAX запрос для получения товаров
Route::get('/api/products', [CatalogController::class, 'productsJson'])->name('catalog.products.json');
Route::get('/api/category/{groupId}/products', [CatalogController::class, 'productsJson'])->name('catalog.category.products.json');
