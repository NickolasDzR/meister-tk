<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'counterparty_id',
        'name',
        'position',
        'phone',
        'email',
        'notes',
    ];

    public function counterparty(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class);
    }
}
