<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\GroupRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Контроллер каталога товаров.
 * Обрабатывает запросы для отображения каталога с сортировкой и пагинацией.
 */
class CatalogController extends Controller
{
    /**
     * @var GroupRepository
     */
    private GroupRepository $groupRepository;

    /**
     * @param GroupRepository $groupRepository
     */
    public function __construct(GroupRepository $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * Главная страница каталога.
     * Отображает группы первого уровня и все товары с сортировкой и пагинацией.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $groups = $this->groupRepository->getFirstLevelGroups();

        // Оптимизация: кэшируем подсчёт товаров на 5 минут
        $productsCountCache = [];
        foreach ($groups as $group) {
            $cacheKey = "group_products_count_{$group->id}";
            $group->products_count = \Cache::remember(
                $cacheKey, 
                300, // 5 минут
                fn() => $this->groupRepository->getTotalProductsCount($group->id)
            );
        }

        $products = $this->getProductsQuery(null, $request);

        return view('catalog.index', compact('groups', 'products'));
    }

    /**
     * Страница категории (группы товаров).
     * Отображает подгруппы и товары выбранной группы.
     *
     * @param Request $request
     * @param int $groupId
     * @return View
     */
    public function category(Request $request, int $groupId): View
    {
        $group = $this->groupRepository->findWithDescendants($groupId);

        if (!$group) {
            abort(404, 'Группа не найдена');
        }

        $groups = $this->groupRepository->getFirstLevelGroups();

        // Оптимизация: кэшируем подсчёт товаров на 5 минут
        foreach ($groups as $g) {
            $cacheKey = "group_products_count_{$g->id}";
            $g->products_count = \Cache::remember(
                $cacheKey,
                300, // 5 минут
                fn() => $this->groupRepository->getTotalProductsCount($g->id)
            );
        }

        // Получаем ID текущей категории и всех её родителей для подсветки в сайдбаре
        $currentGroupId = $groupId;
        $parentGroupIds = $this->groupRepository->getParentGroupIds($groupId);

        // Получаем товары выбранной группы и всех подгрупп
        $products = $this->getProductsQuery($groupId, $request);

        return view('catalog.category', compact('group', 'groups', 'products', 'currentGroupId', 'parentGroupIds'));
    }

    /**
     * Карточка товара.
     *
     * @param int $productId
     * @return View
     */
    public function product(int $productId): View
    {
        $product = Product::with(['price', 'group.parent'])->findOrFail($productId);

        $breadcrumbs = $product->getBreadcrumbs();
        $groups = $this->groupRepository->getFirstLevelGroups();

        // Оптимизация: кэшируем подсчёт товаров на 5 минут
        foreach ($groups as $group) {
            $cacheKey = "group_products_count_{$group->id}";
            $group->products_count = \Cache::remember(
                $cacheKey,
                300, // 5 минут
                fn() => $this->groupRepository->getTotalProductsCount($group->id)
            );
        }

        // Получаем ID категории товара и всех её родителей для подсветки в сайдбаре
        $currentGroupId = $product->id_group;
        $parentGroupIds = $this->groupRepository->getParentGroupIds($product->id_group);

        return view('catalog.product', compact('product', 'breadcrumbs', 'groups', 'currentGroupId', 'parentGroupIds'));
    }

    /**
     * AJAX запрос для получения товаров.
     *
     * @param Request $request
     * @param int|null $groupId
     * @return JsonResponse
     */
    public function productsJson(Request $request, ?int $groupId = null): JsonResponse
    {
        $products = $this->getProductsQuery($groupId, $request);
        
        return response()->json([
            'html' => view('catalog.partials.products-grid', ['products' => $products])->render(),
            'pagination' => view('catalog.partials.pagination', ['products' => $products])->render(),
        ]);
    }

    /**
     * Получить запрос для выборки товаров с сортировкой и пагинацией.
     *
     * @param int|null $groupId
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getProductsQuery(?int $groupId, Request $request)
    {
        $query = Product::with('price');

        // Если указана группа, фильтруем по группе и подгруппам
        if ($groupId) {
            $groupIds = $this->groupRepository->getAllDescendantIds($groupId);
            $query->whereIn('id_group', $groupIds);
        }

        // Сортировка
        $sortField = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');

        $allowedSortFields = ['name', 'price'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }

        $allowedSortOrders = ['asc', 'desc'];
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'asc';
        }

        // Для сортировки по цене присоединяем таблицу prices
        if ($sortField === 'price') {
            $query->leftJoin('prices', 'products.id', '=', 'prices.id_product')
                ->select('products.*')
                ->orderBy('prices.price', $sortOrder);
        } else {
            $query->orderBy($sortField, $sortOrder);
        }

        // Пагинация
        $perPage = $this->getPerPage($request);

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Получить количество товаров на странице.
     *
     * @param Request $request
     * @return int
     */
    private function getPerPage(Request $request): int
    {
        $perPage = (int) $request->get('per_page', 6);
        $allowedPerPage = [6, 12, 18];
        
        return in_array($perPage, $allowedPerPage) ? $perPage : 6;
    }
}
