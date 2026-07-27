<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CounterpartyFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'counterparty_id',
        'name',
        'path',
        'mime_type',
        'size',
        'uploaded_by',
    ];

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }

    /**
     * Пользователь, загрузивший файл.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
