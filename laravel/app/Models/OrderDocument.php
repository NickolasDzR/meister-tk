<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'customer_id',
        'party',
        'type',
        'number',
        'date',
        'deadline',
        'status',
        'postal_tracking',
        'postal_date',
        'return_date',
        'file_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'deadline' => 'date',
            'postal_date' => 'date',
            'return_date' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Строка заказчика в заявке (order_customers), а не контрагент из справочника.
     * Пустая, если документ относится к перевозчику.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(OrderCustomer::class, 'customer_id');
    }
}
