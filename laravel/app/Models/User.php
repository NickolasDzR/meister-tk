<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, MustVerifyEmailTrait, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Все компании пользователя (один аккаунт может быть директором/сотрудником
     * сразу в нескольких компаниях — ИП, ООО и т.д.). Роль и должность
     * хранятся не на пользователе, а на самой связи (pivot), т.к. в разных
     * компаниях у одного человека может быть разная роль.
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(Company::class)
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }

    /**
     * Директор ли пользователь конкретной компании.
     * Используется вместо глобального флага "админ", потому что
     * в одной компании человек может быть admin, а в другой — обычный employee.
     */
    public function isAdminOf(Company $company): bool
    {
        $pivot = $this->companies()
            ->where('companies.id', $company->id)
            ->first()
            ?->pivot;

        return $pivot?->role === 'admin';
    }
}
