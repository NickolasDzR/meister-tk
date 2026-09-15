<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCargo extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Имя таблицы не выводится из имени модели: Laravel ожидал бы order_cargos.
     */
    protected $table = 'order_cargo';

    protected $fillable = [
        'order_id',
        'name',
        'weight',
        'volume',
        'dimensions',
        'packaging_type',
        'packaging_quantity',
        'cost',
        'loading_type',
        'unloading_type',
    ];

    protected function casts(): array
    {
        return [
            'packaging_quantity' => 'integer',
            'cost' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
