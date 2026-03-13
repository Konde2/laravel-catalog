{{-- Список групп в сайдбаре --}}
<div class="sidebar mb-4">
    <h5 class="mb-3">
        <i class="bi bi-folder"></i> Категории
    </h5>
    <div class="list-group">
        <a href="{{ route('catalog.index') }}"
           class="list-group-item list-group-item-action group-link {{ !request('category') ? 'active' : '' }}">
            <i class="bi bi-house"></i> Все товары
        </a>
        @foreach($groups as $group)
            @include('catalog.partials.group-item', ['group' => $group, 'level' => 0])
        @endforeach
    </div>
</div>
