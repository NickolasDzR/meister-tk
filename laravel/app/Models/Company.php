<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'inn',
        'name',
        'kpp',
        'ogrn',
        'address',
        'dadata_status',
    ];

    /**
     * Все пользователи (директор + сотрудники), привязанные к этой компании,
     * вместе с их ролью/должностью именно в этой компании (pivot).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }
}
