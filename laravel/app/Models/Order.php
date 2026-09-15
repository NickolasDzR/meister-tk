<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'number',
        'date',
        'external_number',
        'is_accepted',
        'status',
        'status_changed_at',
        'public_notes',
        'is_note_important',
        'special_conditions',
        'additional_requirements',
        'is_return_trip',
        'is_insured',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'status_changed_at' => 'datetime',
            'is_accepted' => 'boolean',
            'is_note_important' => 'boolean',
            'is_return_trip' => 'boolean',
            'is_insured' => 'boolean',
        ];
    }

    /**
     * Компания, которой принадлежит заявка.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Пользователь, создавший заявку.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Пользователь, последним обновивший заявку.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(OrderCustomer::class);
    }

    public function carriers(): HasMany
    {
        return $this->hasMany(OrderCarrier::class);
    }

    public function cargo(): HasMany
    {
        return $this->hasMany(OrderCargo::class);
    }

    public function waypoints(): HasMany
    {
        return $this->hasMany(OrderWaypoint::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(OrderDocument::class);
    }

    public function rateHistory(): HasMany
    {
        return $this->hasMany(OrderRateHistory::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(OrderExpense::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(OrderFile::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
