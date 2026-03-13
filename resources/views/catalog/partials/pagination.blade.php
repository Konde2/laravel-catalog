{{-- Пагинация --}}
@if($products->hasPages())
    <nav aria-label="Пагинация товаров" class="mt-4">
        <ul class="pagination justify-content-center">
            {{-- Кнопка "Назад" --}}
            @if($products->onFirstPage())
                <li class="page-item disabled">
                    <span class="page-link">&laquo; Назад</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $products->previousPageUrl() }}" data-page="prev">&laquo; Назад</a>
                </li>
            @endif

            {{-- Номера страниц --}}
            @foreach($products->links()->elements[0] ?? [] as $page => $url)
                @if(is_numeric($page))
                    @if($page == $products->currentPage())
                        <li class="page-item active">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $url }}" data-page="{{ $page }}">{{ $page }}</a>
                        </li>
                    @endif
                @endif
            @endforeach

            {{-- Кнопка "Вперед" --}}
            @if($products->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $products->nextPageUrl() }}" data-page="next">Вперед &raquo;</a>
                </li>
            @else
                <li class="page-item disabled">
                    <span class="page-link">Вперед &raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif
