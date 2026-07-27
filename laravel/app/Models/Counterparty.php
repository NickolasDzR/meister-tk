<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Counterparty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'inn',
        'name',
        'short_name',
        'type',
        'city',
        'legal_address',
        'actual_address',
        'address_matches',
        'ogrn',
        'kpp',
        'ati_code',
        'phone',
        'email',
        'website',
        'edo_identifier',
        'notes',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'address_matches' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Компания, которой принадлежит этот контрагент.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Пользователь, создавший запись.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Пользователь, последним обновивший запись.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CounterpartyFile::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
