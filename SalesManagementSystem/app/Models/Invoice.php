<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Invoice extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'invoices';

    const STATUS_DRAFT     = 'draft';
    const STATUS_SENT      = 'sent';
    const STATUS_PAID      = 'paid';
    const STATUS_OVERDUE   = 'overdue';
    const STATUS_PARTIAL   = 'partial';
    const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'number',
        'customer_id',
        'quotation_id',
        'deal_id',
        'created_by',
        'status',
        'items',            // array of line items
        'subtotal',
        'tax_rate',
        'tax_amount',
        'discount_type',
        'discount_value',
        'discount_amount',
        'total',
        'amount_paid',
        'amount_due',
        'currency',
        'due_date',
        'paid_at',
        'payment_method',
        'payment_reference',
        'notes',
        'terms',
        'sent_at',
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
            'amount_paid'    => 'float',
            'amount_due'     => 'float',
            'due_date'       => 'datetime',
            'paid_at'        => 'datetime',
            'sent_at'        => 'datetime',
        ];
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast()
            && ! in_array($this->status, [self::STATUS_PAID, self::STATUS_CANCELLED]);
    }

    public function scopeForCustomer($query, $customerId) { return $query->where('customer_id', $customerId); }
    public function scopePaid($query)    { return $query->where('status', self::STATUS_PAID); }
    public function scopeOverdue($query) { return $query->where('status', self::STATUS_OVERDUE); }
    public function scopeUnpaid($query)  { return $query->whereIn('status', [self::STATUS_SENT, self::STATUS_OVERDUE, self::STATUS_PARTIAL]); }
}
