<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Модель товара
 * 
 * @property int $id
 * @property int $id_group
 * @property string $name
 * @property Price|null $price
 * @property Group $group
 */
class Product extends Model
{
    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_group',
        'name',
    ];

    /**
     * Получить группу товара.
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'id_group');
    }

    /**
     * Получить цену товара.
     */
    public function price(): HasOne
    {
        return $this->hasOne(Price::class, 'id_product');
    }

    /**
     * Получить путь хлебных крошек (все родительские группы).
     *
     * @return array<Group>
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = [];
        $currentGroup = $this->group;
        
        while ($currentGroup) {
            array_unshift($breadcrumbs, $currentGroup);
            $currentGroup = $currentGroup->parent;
        }
        
        return $breadcrumbs;
    }
}
