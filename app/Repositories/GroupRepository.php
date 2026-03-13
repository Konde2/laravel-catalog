<?php

namespace App\Repositories;

use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;

/**
 * Репозиторий для работы с группами товаров.
 * Реализует принцип Repository Pattern для отделения бизнес-логики от моделей.
 */
class GroupRepository
{
    /**
     * @var Group
     */
    private Group $group;

    /**
     * @param Group $group
     */
    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    /**
     * Получить все группы первого уровня.
     *
     * @return Collection<int, Group>
     */
    public function getFirstLevelGroups(): Collection
    {
        return $this->group->with(['children.children.children.children'])
            ->firstLevel()
            ->orderBy('name')
            ->get();
    }

    /**
     * Получить группу по ID с загрузкой связей.
     *
     * @param int $id
     * @return Group|null
     */
    public function find(int $id): ?Group
    {
        return $this->group->with(['children', 'parent'])->find($id);
    }

    /**
     * Получить группу по ID с загрузкой всех дочерних групп.
     *
     * @param int $id
     * @return Group|null
     */
    public function findWithDescendants(int $id): ?Group
    {
        return $this->group->with(['children.children.children', 'parent'])->find($id);
    }

    /**
     * Получить общее количество товаров в группе и всех её подгруппах.
     *
     * @param int $groupId
     * @return int
     */
    public function getTotalProductsCount(int $groupId): int
    {
        $group = $this->findWithDescendants($groupId);
        
        if (!$group) {
            return 0;
        }
        
        return $group->getTotalProductsCount();
    }

    /**
     * Получить все ID подгрупп (включая саму группу).
     *
     * @param int $groupId
     * @return array<int>
     */
    public function getAllDescendantIds(int $groupId): array
    {
        $group = $this->findWithDescendants($groupId);
        
        if (!$group) {
            return [];
        }
        
        return $group->getAllDescendantIds();
    }

    /**
     * Получить хлебные крошки для группы.
     *
     * @param Group $group
     * @return array<Group>
     */
    public function getBreadcrumbs(Group $group): array
    {
        $breadcrumbs = [];
        $current = $group;
        
        while ($current && $current->id) {
            array_unshift($breadcrumbs, $current);
            $current = $current->parent;
        }
        
        return $breadcrumbs;
    }

    /**
     * Получить ID всех родительских категорий для данной группы.
     *
     * @param int $groupId
     * @return array<int>
     */
    public function getParentGroupIds(int $groupId): array
    {
        $group = $this->find($groupId);
        
        if (!$group) {
            return [];
        }
        
        $ids = [];
        $current = $group->parent;
        
        while ($current && $current->id) {
            $ids[] = $current->id;
            $current = $current->parent;
        }
        
        return $ids;
    }
}
