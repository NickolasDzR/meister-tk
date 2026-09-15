<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderWaypoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'type',
        'counterparty_name',
        'address',
        'contact_person',
        'contact_phone',
        'additional_info',
        'date',
        'time',
        'sequence',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'sequence' => 'integer',
            // time остаётся строкой "10:30:00": колонка TIME без даты,
            // Carbon подставил бы к ней сегодняшнее число.
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
