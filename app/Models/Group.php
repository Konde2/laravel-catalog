<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Collection;

/**
 * Модель группы товаров
 * 
 * @property int $id
 * @property int $id_parent
 * @property string $name
 * @property Collection<Group> $children
 * @property Collection<Product> $products
 * @property Group|null $parent
 */
class Group extends Model
{
    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_parent',
        'name',
    ];

    /**
     * Получить все дочерние группы.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Group::class, 'id_parent');
    }

    /**
     * Получить родительскую группу.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_parent');
    }

    /**
     * Получить товары в этой группе.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'id_group');
    }

    /**
     * Получить все ID групп (эту и все подгруппы рекурсивно).
     *
     * @return array<int>
     */
    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        
        foreach ($this->children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        
        return $ids;
    }

    /**
     * Получить общее количество товаров в этой группе и всех подгруппах.
     */
    public function getTotalProductsCount(): int
    {
        $groupIds = $this->getAllDescendantIds();
        
        return Product::whereIn('id_group', $groupIds)->count();
    }

    /**
     * Получить товары из этой группы и всех подгрупп.
     */
    public function getAllProducts(): \Illuminate\Database\Eloquent\Builder
    {
        $groupIds = $this->getAllDescendantIds();
        
        return Product::with('price')->whereIn('id_group', $groupIds);
    }

    /**
     * Получить группы первого уровня (у которых id_parent = 0).
     */
    public function scopeFirstLevel($query)
    {
        return $query->where('id_parent', 0);
    }
}
