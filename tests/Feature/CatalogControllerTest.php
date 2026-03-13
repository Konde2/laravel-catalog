<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Тесты для CatalogController.
 */
class CatalogControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: главная страница каталога доступна.
     */
    public function test_index_page_is_available(): void
    {
        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertViewIs('catalog.index');
    }

    /**
     * Тест: главная страница отображает группы первого уровня.
     */
    public function test_index_page_shows_first_level_groups(): void
    {
        $group1 = Group::create(['id_parent' => 0, 'name' => 'Группа 1']);
        $group2 = Group::create(['id_parent' => 0, 'name' => 'Группа 2']);
        Group::create(['id_parent' => $group1->id, 'name' => 'Подгруппа']);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Группа 1');
        $response->assertSee('Группа 2');
        $response->assertDontSee('Подгруппа');
    }

    /**
     * Тест: главная страница отображает товары.
     */
    public function test_index_page_shows_products(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Тестовый товар']);
        Price::create(['id_product' => $product->id, 'price' => 1000]);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Тестовый товар');
        $response->assertSee('1 000');
    }

    /**
     * Тест: страница категории доступна.
     */
    public function test_category_page_is_available(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Категория']);

        $response = $this->get(route('catalog.category', $group->id));

        $response->assertStatus(200);
        $response->assertViewIs('catalog.category');
        $response->assertSee('Категория');
    }

    /**
     * Тест: страница категории отображает подгруппы.
     */
    public function test_category_page_shows_subcategories(): void
    {
        $parent = Group::create(['id_parent' => 0, 'name' => 'Родитель']);
        $child = Group::create(['id_parent' => $parent->id, 'name' => 'Дочерняя']);

        $response = $this->get(route('catalog.category', $parent->id));

        $response->assertStatus(200);
        $response->assertSee('Дочерняя');
    }

    /**
     * Тест: страница категории возвращает 404 для несуществующей группы.
     */
    public function test_category_page_returns_404_for_nonexistent_group(): void
    {
        $response = $this->get(route('catalog.category', 99999));

        $response->assertStatus(404);
    }

    /**
     * Тест: страница товара доступна.
     */
    public function test_product_page_is_available(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);
        Price::create(['id_product' => $product->id, 'price' => 1500]);

        $response = $this->get(route('catalog.product', $product->id));

        $response->assertStatus(200);
        $response->assertViewIs('catalog.product');
        $response->assertSee('Товар');
        $response->assertSee('1 500');
    }

    /**
     * Тест: страница товара отображает хлебные крошки.
     */
    public function test_product_page_shows_breadcrumbs(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);
        $product = Product::create(['id_group' => $child->id, 'name' => 'Товар']);

        $response = $this->get(route('catalog.product', $product->id));

        $response->assertStatus(200);
        $response->assertSee('Корень');
        $response->assertSee('Дочерняя');
    }

    /**
     * Тест: AJAX запрос возвращает JSON.
     */
    public function test_products_json_returns_json(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);
        Price::create(['id_product' => $product->id, 'price' => 1000]);

        $response = $this->getJson(route('catalog.products.json'));

        $response->assertStatus(200);
        $response->assertJsonStructure(['html', 'pagination']);
    }

    /**
     * Тест: сортировка товаров по цене.
     */
    public function test_products_sorted_by_price(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product1 = Product::create(['id_group' => $group->id, 'name' => 'Товар 1']);
        $product2 = Product::create(['id_group' => $group->id, 'name' => 'Товар 2']);
        $product3 = Product::create(['id_group' => $group->id, 'name' => 'Товар 3']);

        Price::create(['id_product' => $product1->id, 'price' => 3000]);
        Price::create(['id_product' => $product2->id, 'price' => 1000]);
        Price::create(['id_product' => $product3->id, 'price' => 2000]);

        $response = $this->getJson(route('catalog.products.json', ['sort' => 'price', 'order' => 'asc']));

        $response->assertStatus(200);
        $this->assertStringContainsString('Товар 2', $response['html']);
    }

    /**
     * Тест: сортировка товаров по названию.
     */
    public function test_products_sorted_by_name(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        Product::create(['id_group' => $group->id, 'name' => 'Б Товар']);
        Product::create(['id_group' => $group->id, 'name' => 'А Товар']);
        Product::create(['id_group' => $group->id, 'name' => 'В Товар']);

        $response = $this->getJson(route('catalog.products.json', ['sort' => 'name', 'order' => 'asc']));

        $response->assertStatus(200);
        $this->assertStringContainsString('А Товар', $response['html']);
    }

    /**
     * Тест: пагинация с разным количеством товаров на странице.
     */
    public function test_pagination_with_different_per_page(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        
        for ($i = 1; $i <= 15; $i++) {
            $product = Product::create(['id_group' => $group->id, 'name' => "Товар $i"]);
            Price::create(['id_product' => $product->id, 'price' => $i * 100]);
        }

        // Тест с 6 товарами на странице
        $response = $this->getJson(route('catalog.products.json', ['per_page' => 6]));
        $response->assertStatus(200);

        // Тест с 12 товарами на странице
        $response = $this->getJson(route('catalog.products.json', ['per_page' => 12]));
        $response->assertStatus(200);

        // Тест с 18 товарами на странице
        $response = $this->getJson(route('catalog.products.json', ['per_page' => 18]));
        $response->assertStatus(200);
    }

    /**
     * Тест: фильтрация товаров по категории.
     */
    public function test_products_filtered_by_category(): void
    {
        $group1 = Group::create(['id_parent' => 0, 'name' => 'Группа 1']);
        $group2 = Group::create(['id_parent' => 0, 'name' => 'Группа 2']);
        
        $product1 = Product::create(['id_group' => $group1->id, 'name' => 'Товар 1']);
        $product2 = Product::create(['id_group' => $group2->id, 'name' => 'Товар 2']);
        
        Price::create(['id_product' => $product1->id, 'price' => 1000]);
        Price::create(['id_product' => $product2->id, 'price' => 2000]);

        $response = $this->getJson(route('catalog.category.products.json', $group1->id));

        $response->assertStatus(200);
        $this->assertStringContainsString('Товар 1', $response['html']);
        $response->assertDontSee('Товар 2');
    }
}
