<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Тесты для модели Product.
 */
class ProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: создание товара.
     */
    public function test_product_can_be_created(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Тестовая группа']);
        
        $product = Product::create([
            'id_group' => $group->id,
            'name' => 'Тестовый товар',
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Тестовый товар',
        ]);
    }

    /**
     * Тест: связь с группой.
     */
    public function test_product_belongs_to_group(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);

        $this->assertEquals($group->id, $product->group->id);
    }

    /**
     * Тест: связь с ценой.
     */
    public function test_product_has_price(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);
        $price = Price::create(['id_product' => $product->id, 'price' => 1500.50]);

        $this->assertEquals($price->price, $product->price->price);
    }

    /**
     * Тест: получение хлебных крошек.
     */
    public function test_get_breadcrumbs(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);
        $product = Product::create(['id_group' => $child->id, 'name' => 'Товар']);

        $breadcrumbs = $product->getBreadcrumbs();

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals($root->id, $breadcrumbs[0]->id);
        $this->assertEquals($child->id, $breadcrumbs[1]->id);
    }

    /**
     * Тест: форматирование цены.
     */
    public function test_price_formatted(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);
        $price = Price::create(['id_product' => $product->id, 'price' => 1234.56]);

        $this->assertEquals('1 234.56 ₽', $price->formatted_price);
    }

    /**
     * Тест: каскадное удаление товара при удалении группы.
     */
    public function test_product_deleted_when_group_deleted(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);

        $group->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /**
     * Тест: каскадное удаление цены при удалении товара.
     */
    public function test_price_deleted_when_product_deleted(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Группа']);
        $product = Product::create(['id_group' => $group->id, 'name' => 'Товар']);
        $price = Price::create(['id_product' => $product->id, 'price' => 1000]);

        $product->delete();

        $this->assertDatabaseMissing('prices', ['id_product' => $product->id]);
    }
}
