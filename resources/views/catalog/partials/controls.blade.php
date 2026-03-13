{{-- Элементы управления: сортировка и количество товаров на странице --}}
<div class="row mb-3 align-items-center">
    <div class="col-md-6 per-page-controls">
        <label for="per_page" class="form-label mb-1">
            <i class="bi bi-grid-3x3-gap"></i> Товаров на странице:
        </label>
        <select id="per_page" class="form-select form-select-sm" onchange="updatePerPage(this.value)">
            <option value="6" {{ request('per_page', 6) == 6 ? 'selected' : '' }}>6</option>
            <option value="12" {{ request('per_page') == 12 ? 'selected' : '' }}>12</option>
            <option value="18" {{ request('per_page') == 18 ? 'selected' : '' }}>18</option>
        </select>
    </div>
    <div class="col-md-6 sort-controls text-md-end">
        <label for="sort" class="form-label mb-1">
            <i class="bi bi-sort-down"></i> Сортировка:
        </label>
        <div class="d-flex gap-2 justify-content-md-end">
            <select id="sort_field" class="form-select form-select-sm" onchange="applySorting()">
                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>По названию</option>
                <option value="price" {{ request('sort') === 'price' ? 'selected' : '' }}>По цене</option>
            </select>
            <select id="sort_order" class="form-select form-select-sm" onchange="applySorting()">
                <option value="asc" {{ request('order') === 'asc' ? 'selected' : '' }}>По возрастанию</option>
                <option value="desc" {{ request('order') === 'desc' ? 'selected' : '' }}>По убыванию</option>
            </select>
        </div>
    </div>
</div>
