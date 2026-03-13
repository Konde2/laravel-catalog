{{-- Сетка товаров --}}
@forelse($products as $product)
    <div class="col-md-4 mb-4">
        <div class="card product-card">
            <div class="card-body">
                <h5 class="card-title product-name">
                    <a href="{{ route('catalog.product', $product->id) }}" class="text-decoration-none">
                        {{ $product->name }}
                    </a>
                </h5>
                @if($product->price)
                    <p class="product-price mb-0">{{ number_format($product->price->price, 2, '.', ' ') }} ₽</p>
                @else
                    <p class="text-muted mb-0">Цена не указана</p>
                @endif
            </div>
            <div class="card-footer bg-white border-0">
                <a href="{{ route('catalog.product', $product->id) }}" class="btn btn-primary w-100">
                    <i class="bi bi-eye"></i> Подробнее
                </a>
            </div>
        </div>
    </div>
@empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            <i class="bi bi-info-circle"></i> В этой категории пока нет товаров
        </div>
    </div>
@endforelse
