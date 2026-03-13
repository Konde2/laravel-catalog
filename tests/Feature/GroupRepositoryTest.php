<?php

namespace Tests\Feature;

use App\Models\Group;
use App\Models\Product;
use App\Models\Price;
use App\Repositories\GroupRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Тесты для GroupRepository.
 */
class GroupRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private GroupRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(GroupRepository::class);
    }

    /**
     * Тест: получение групп первого уровня.
     */
    public function test_get_first_level_groups(): void
    {
        Group::create(['id_parent' => 0, 'name' => 'Группа 1']);
        Group::create(['id_parent' => 0, 'name' => 'Группа 2']);
        Group::create(['id_parent' => 1, 'name' => 'Подгруппа']);

        $groups = $this->repository->getFirstLevelGroups();

        $this->assertCount(2, $groups);
        $this->assertEquals(['Группа 1', 'Группа 2'], $groups->pluck('name')->toArray());
    }

    /**
     * Тест: поиск группы по ID.
     */
    public function test_find_group(): void
    {
        $group = Group::create(['id_parent' => 0, 'name' => 'Тестовая группа']);

        $found = $this->repository->find($group->id);

        $this->assertNotNull($found);
        $this->assertEquals($group->id, $found->id);
        $this->assertEquals($group->name, $found->name);
    }

    /**
     * Тест: поиск несуществующей группы.
     */
    public function test_find_nonexistent_group(): void
    {
        $found = $this->repository->find(99999);

        $this->assertNull($found);
    }

    /**
     * Тест: получение группы с потомками.
     */
    public function test_find_with_descendants(): void
    {
        $parent = Group::create(['id_parent' => 0, 'name' => 'Родитель']);
        $child = Group::create(['id_parent' => $parent->id, 'name' => 'Дочерняя']);

        $group = $this->repository->findWithDescendants($parent->id);

        $this->assertNotNull($group);
        $this->assertEquals(1, $group->children->count());
    }

    /**
     * Тест: получение общего количества товаров.
     */
    public function test_get_total_products_count(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);

        Product::create(['id_group' => $root->id, 'name' => 'Товар 1']);
        Product::create(['id_group' => $child->id, 'name' => 'Товар 2']);
        Product::create(['id_group' => $child->id, 'name' => 'Товар 3']);

        $count = $this->repository->getTotalProductsCount($root->id);

        $this->assertEquals(3, $count);
    }

    /**
     * Тест: получение ID всех подгрупп.
     */
    public function test_get_all_descendant_ids(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child1 = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя 1']);
        $child2 = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя 2']);

        $ids = $this->repository->getAllDescendantIds($root->id);

        $this->assertContains($root->id, $ids);
        $this->assertContains($child1->id, $ids);
        $this->assertContains($child2->id, $ids);
        $this->assertCount(3, $ids);
    }

    /**
     * Тест: получение хлебных крошек для группы.
     */
    public function test_get_breadcrumbs(): void
    {
        $root = Group::create(['id_parent' => 0, 'name' => 'Корень']);
        $child = Group::create(['id_parent' => $root->id, 'name' => 'Дочерняя']);

        $breadcrumbs = $this->repository->getBreadcrumbs($child);

        $this->assertCount(2, $breadcrumbs);
        $this->assertEquals($root->id, $breadcrumbs[0]->id);
        $this->assertEquals($child->id, $breadcrumbs[1]->id);
    }
}
