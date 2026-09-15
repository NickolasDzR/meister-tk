<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCarrier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_carriers';

    protected $fillable = [
        'order_id',
        'carrier_id',
        'contact_person',
        'legal_entity',
        'contract_number',
        'contract_date',
        'vehicle',
        'vehicle_plate',
        'trailer',
        'trailer_plate',
        'driver_1_name',
        'driver_1_phone',
        'driver_2_name',
        'driver_2_phone',
        'driver_1_id',
        'driver_2_id',
        'vehicle_id',
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
        'documents_received',
        'financial_documents_received',
        'documents_received_date',
        'payment_deadline',
    ];

    protected function casts(): array
    {
        return [
            'contract_date' => 'date',
            'rate' => 'decimal:2',
            'rate_quantity' => 'integer',
            'rate_price' => 'decimal:2',
            'payment_days' => 'integer',
            'prepayment_amount' => 'decimal:2',
            'documents_received' => 'boolean',
            'financial_documents_received' => 'boolean',
            'documents_received_date' => 'date',
            'payment_deadline' => 'date',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Контрагент-перевозчик из справочника.
     */
    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Counterparty::class, 'carrier_id');
    }

    /**
     * Водитель из справочника. Может быть null: запись справочника удалили,
     * либо заявку завели до появления справочников — тогда остаются только
     * текстовые driver_1_name / driver_1_phone.
     */
    public function driver1(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_1_id');
    }

    public function driver2(): BelongsTo
    {
        return $this->belongsTo(Driver::class, 'driver_2_id');
    }

    /**
     * Машина из справочника. Связь НЕ может называться vehicle(): рядом есть
     * текстовая колонка vehicle со слепком названия, и $oc->vehicle вернул бы
     * строку, а не модель — атрибут всегда перекрывает связь.
     * Прицеп отдельной ссылки не имеет: он часть машины (vehicles.trailer_plate),
     * а в заявке лежит слепком.
     */
    public function vehicleCard(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}
