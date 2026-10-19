<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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
     * Кого пускать в админку. Без этого метода Filament на проде
     * (APP_ENV=production) отвечает 403 всем, включая владельца, —
     * пускает без него только локально.
     *
     * Вошедшего по логину и паролю пускаем всегда: регистрации на сайте
     * нет, аккаунты заводятся только из консоли (make app-add-admin) и все
     * они — для админки. Посторонний без пароля сюда не доходит.
     *
     * Когда в users появятся сотрудники CRM, здесь нужен будет отбор
     * (например, по списку почт), иначе они попадут в админку статей.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
