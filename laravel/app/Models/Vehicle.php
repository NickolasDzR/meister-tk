<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'counterparty_id',
        'type',
        'brand',
        'model',
        'plate',
        'vin',
        'sts_number',
        'body_type',
        'capacity_tons',
        'volume_m3',
        'dimensions',
        'ownership',
        'has_trailer',
        'trailer_brand',
        'trailer_plate',
        'trailer_vin',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity_tons' => 'decimal:2',
            'volume_m3' => 'decimal:2',
            'has_trailer' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Перевозчик, которому принадлежит машина.
     */
    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    /**
     * Заявки, в которых стоит эта машина.
     * Обратная сторона OrderCarrier::vehicleCard().
     */
    public function orderCarriers(): HasMany
    {
        return $this->hasMany(OrderCarrier::class, 'vehicle_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
