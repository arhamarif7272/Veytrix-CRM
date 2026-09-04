<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Quotation extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'quotations';

    const STATUS_DRAFT    = 'draft';
    const STATUS_SENT     = 'sent';
    const STATUS_ACCEPTED = 'accepted';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED  = 'expired';

    protected $fillable = [
        'number',
        'customer_id',
        'deal_id',
        'created_by',
        'status',
        'items',            // array of line items
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',    // 'percentage'|'fixed'
        'discount_value',
        'discount_amount',
        'total',
        'currency',
        'notes',
        'terms',
        'valid_until',
        'sent_at',
        'invoice_id',       // set when converted to invoice
    ];

    protected function casts(): array
    {
        return [
            'items'          => 'array',
            'subtotal'       => 'float',
            'tax_rate'       => 'float',
            'tax_amount'     => 'float',
            'discount_value' => 'float',
            'discount_amount'=> 'float',
            'total'          => 'float',
            'valid_until'    => 'datetime',
            'sent_at'        => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->valid_until && $this->valid_until->isPast()
            && $this->status === self::STATUS_SENT;
    }

    public function scopeForCustomer($query, $customerId) { return $query->where('customer_id', $customerId); }
}
