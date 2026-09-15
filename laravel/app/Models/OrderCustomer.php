<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'customer_id',
        'contact_person',
        'legal_entity',
        'contract_number',
        'rate_type',
        'rate',
        'rate_unit',
        'rate_quantity',
        'rate_price',
        'payment_form',
        'payment_terms',
        'payment_days',
        'payment_days_type',
        'prepayment_amount',
        'prepayment_form',
        'documents_sent',
        'financial_documents_sent',
        'documents_sent_date',
        'payment_deadline',
        'additional_items',
    ];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:2',
            'rate_quantity' => 'integer',
            'rate_price' => 'decimal:2',
            'payment_days' => 'integer',
            'prepayment_amount' => 'decimal:2',
            'documents_sent' => 'boolean',
            'financial_documents_sent' => 'boolean',
            'documents_sent_date' => 'date',
            'payment_deadline' => 'date',
            'additional_items' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Контрагент-заказчик из справочника.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class, 'customer_id');
    }

    /**
     * Документы, относящиеся именно к этому заказчику в заявке.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(OrderDocument::class, 'customer_id');
    }

    public function rateHistory(): HasMany
    {
        return $this->hasMany(OrderRateHistory::class, 'customer_id');
    }
}
