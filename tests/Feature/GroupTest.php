<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Product;
use App\Models\Price;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Тесты для модели Group.
 */
class GroupTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Тест: создание группы.
     */
    public function test_group_can_be_created(): void
    {
        $group = Group::create([
            'id_parent' => 0,
            'name' => 'Тестовая группа',
        ]);

        $this->assertDatabaseHas('groups', [
            'id' => $group->id,
            'name' => 'Тестовая группа',
            'id_parent' => 0,
        ]);
    }

    /**
     * Тест: получение групп первого уровня.
     */
    public function test_first_level_groups(): void
    {
        Group::create(['id_parent' => 0, 'name' => 'Группа 1']);
        Group::create(['id_parent' => 0, 'name' => 'Группа 2']);
        Group::create(['id_parent' => 1, 'name' => 'Подгруппа 1']);

        $firstLevelGroups = Group::firstLevel()->get();

        $this->assertCount(2, $firstLevelGroups);
        $this->assertEquals(['Группа 1', 'Группа 2'], $firstLevelGroups->pluck('name')->toArray());
    }

    /**
     * Тест: связь с дочерними группами.
     */
    public function test_group_has_children(): void
    {
        $parent = Group::create(['id_parent' => 0, 'name' => 'Родитель']);
        Group::create(['id_parent' => $parent->id, 'name' => 'Дочерняя 1']);
        Group::create(['id_parent' => $parent->id, 'name' => 'Дочерняя 2']);

        $this->assertEquals(2, $parent->children->count());
    }

    /**
     * Тест: связь с родительской группой.
     */
    public function test_group_has_parent(): void
    {
        $parent = Group::create(['id_parent' => 0, 'name' => 'Родитель']);
        $child = Group::create(['id_parent' => $parent->id, 'name' => 'Дочерняя']);

        $this->assertEquals($parent->id, $child->parent->id);
    }

    /**
     * Тест: получение всех ID подгрупп рекурсивно.
     */
    public function test_get_all_descendant_ids(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child1 = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя 1']);
        $child2 = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя 2']);
        $grandchild = Group::create(['id_parent' => $child1->id, 'name' => 'Внучатая']);

        $ids = $root->getAllDescendantIds();

        $this->assertContains($root->id, $ids);
        $this->assertContains($child1->id, $ids);
        $this->assertContains($child2->id, $ids);
        $this->assertContains($grandchild->id, $ids);
        $this->assertCount(4, $ids);
    }

    /**
     * Тест: подсчет общего количества товаров в группе и подгруппах.
     */
    public function test_get_total_products_count(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);

        $product1 = Product::create(['id_group' => $root->id, 'name' => 'Товар 1']);
        $product2 = Product::create(['id_group' => $child->id, 'name' => 'Товар 2']);
        $product3 = Product::create(['id_group' => $child->id, 'name' => 'Товар 3']);

        $this->assertEquals(3, $root->getTotalProductsCount());
        $this->assertEquals(2, $child->getTotalProductsCount());
    }

    /**
     * Тест: получение товаров из группы и всех подгрупп.
     */
    public function test_get_all_products(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);

        $product1 = Product::create(['id_group' => $root->id, 'name' => 'Товар 1']);
        $product2 = Product::create(['id_group' => $child->id, 'name' => 'Товар 2']);

        Price::create(['id_product' => $product1->id, 'price' => 100]);
        Price::create(['id_product' => $product2->id, 'price' => 200]);

        $products = $root->getAllProducts()->get();

        $this->assertCount(2, $products);
        $this->assertTrue($products->contains($product1));
        $this->assertTrue($products->contains($product2));
    }
}
