<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRateHistory extends Model
{
    use HasFactory;

    /**
     * Имя таблицы не выводится из имени модели: Laravel ожидал бы order_rate_histories.
     */
    protected $table = 'order_rate_history';

    /**
     * История пишется один раз и не редактируется — в таблице только created_at.
     * Без этого Eloquent попытается записать несуществующий updated_at.
     */
    const UPDATED_AT = null;

    protected $fillable = [
        'order_id',
        'customer_id',
        'party',
        'old_rate',
        'new_rate',
        'reason',
        'changed_by',
    ];

    protected function casts(): array
    {
        return [
            'old_rate' => 'decimal:2',
            'new_rate' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Строка заказчика в заявке — заполнена, если менялась ставка заказчика.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(OrderCustomer::class, 'customer_id');
    }

    /**
     * Пользователь, изменивший ставку.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
