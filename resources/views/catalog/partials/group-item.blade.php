{{-- Элемент категории с поддержкой вложенности --}}
@php
    // Проверяем, является ли категория активной или находится в ветке к активной
    $isInBranch = isset($parentGroupIds) && in_array($group->id, $parentGroupIds);
    $isActive = isset($currentGroupId) && $currentGroupId == $group->id;
    $hasChildren = $group->children && $group->children->count() > 0;
    $shouldExpand = $isActive || $isInBranch; // Разворачиваем если активна или в ветке
@endphp

<div>
    <a href="{{ route('catalog.category', $group->id) }}"
       class="list-group-item list-group-item-action group-link {{ $isActive ? 'active' : '' }}"
       style="{{ $level > 0 ? 'padding-left: ' . (($level * 24) + 16) . 'px;' : '' }}">
        @if($level > 0)
            <i class="bi bi-caret-right-fill text-muted me-1" style="font-size: 0.7em;"></i>
        @else
            <i class="bi bi-folder me-1"></i>
        @endif
        {{ $group->name }}
        @if(isset($group->products_count))
            <span class="products-count float-end">({{ $group->products_count }})</span>
        @endif
    </a>
    
    {{-- Рекурсивное отображение подкатегорий - разворачиваем если в активной ветке --}}
    @if($hasChildren && $shouldExpand)
        <div class="subcategories">
            @foreach($group->children as $child)
                @include('catalog.partials.group-item', ['group' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
