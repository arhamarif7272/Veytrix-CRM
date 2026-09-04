<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class TicketMessage extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'ticket_messages';

    const SENDER_STAFF    = 'staff';
    const SENDER_CUSTOMER = 'customer';

    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_name',
        'sender_role',      // 'staff'|'customer'
        'message',
        'attachments',      // array of file paths
        'is_internal',      // staff-only note
    ];

    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'is_internal' => 'boolean',
        ];
    }

    public function scopeForTicket($query, $ticketId) { return $query->where('ticket_id', $ticketId); }
    public function scopePublic($query) { return $query->where('is_internal', '!=', true); }
}
