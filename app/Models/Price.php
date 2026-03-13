<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Модель цены товара
 * 
 * @property int $id
 * @property int $id_product
 * @property float $price
 * @property Product $product
 */
class Price extends Model
{
    /**
     * Атрибуты, которые можно массово назначать.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_product',
        'price',
    ];

    /**
     * Получить товар.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'id_product');
    }

    /**
     * Форматировать цену для отображения.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2, '.', ' ') . ' ₽';
    }
}
