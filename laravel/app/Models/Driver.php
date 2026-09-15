<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'counterparty_id',
        'last_name',
        'first_name',
        'middle_name',
        'phone',
        'birth_date',
        'inn',
        'license_series',
        'license_number',
        'license_issued_at',
        'license_expires_at',
        'license_categories',
        'passport_series',
        'passport_number',
        'passport_issued_by',
        'passport_issued_at',
        'passport_department_code',
        'registration_address',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'license_issued_at' => 'date',
            'license_expires_at' => 'date',
            'passport_issued_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Перевозчик, которому принадлежит водитель.
     */
    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    /**
     * Заявки, где водитель идёт первым.
     */
    public function orderCarriersAsFirst(): HasMany
    {
        return $this->hasMany(OrderCarrier::class, 'driver_1_id');
    }

    /**
     * Заявки, где водитель идёт вторым (сменщик).
     */
    public function orderCarriersAsSecond(): HasMany
    {
        return $this->hasMany(OrderCarrier::class, 'driver_2_id');
    }

    /**
     * «Ковалёв Дмитрий Сергеевич» — для списков выбора и печатных форм.
     */
    protected function fullName(): Attribute
    {
        return Attribute::get(fn (): string => trim(implode(' ', array_filter([
            $this->last_name,
            $this->first_name,
            $this->middle_name,
        ]))));
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
