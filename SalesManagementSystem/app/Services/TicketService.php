<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Ticket;
use App\Models\TicketMessage;
use App\Models\User;

class TicketService
{
    public function generateTicketNumber(): string
    {
        $count = Ticket::count() + 1;
        return 'TIK-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    public function createTicket(array $data, User $user): Ticket
    {
        $data['ticket_number'] = $this->generateTicketNumber();
        $data['created_by'] = $user->id;
        $data['status'] = Ticket::STATUS_OPEN;

        if ($user->isCustomer() && empty($data['customer_id'])) {
            $data['customer_id'] = $user->id;
        }

        $ticket = Ticket::create($data);

        // If description provided, create first message
        if (!empty($data['description'])) {
            TicketMessage::create([
                'ticket_id'   => (string) $ticket->id,
                'sender_id'   => $user->id,
                'sender_role' => $user->role,
                'message'     => $data['description'],
                'created_at'  => now(),
            ]);
        }

        AuditService::log(
            action: 'ticket.created',
            module: 'tickets',
            entityType: 'Ticket',
            entityId: (string) $ticket->id,
            entityLabel: $ticket->ticket_number,
            newValues: $ticket->toArray()
        );

        if (!empty($ticket->assigned_to) && $ticket->assigned_to !== $user->id) {
            NotificationService::send(
                userId: $ticket->assigned_to,
                type: 'ticket_assigned',
                title: 'Ticket Assigned',
                message: "Ticket {$ticket->ticket_number} has been assigned to you",
                relatedType: 'ticket',
                relatedId: (string) $ticket->id,
                actionUrl: route('tickets.show', $ticket->id)
            );
        }

        return $ticket;
    }

    public function addMessage(Ticket $ticket, string $message, User $user, array $attachments = []): TicketMessage
    {
        $ticketMessage = TicketMessage::create([
            'ticket_id'   => (string) $ticket->id,
            'sender_id'   => $user->id,
            'sender_role' => $user->role,
            'message'     => $message,
            'attachments' => $attachments,
            'created_at'  => now(),
        ]);

        // If support responds for the first time
        if (!$user->isCustomer() && !$ticket->first_response_at) {
            $ticket->update(['first_response_at' => now()]);
        }

        // Auto update status if waiting on customer vs in progress
        if ($user->isCustomer() && $ticket->status === Ticket::STATUS_WAITING) {
            $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
        }

        // Notify other party
        $recipientId = $user->isCustomer() ? $ticket->assigned_to : $ticket->created_by;
        if ($recipientId && $recipientId !== $user->id) {
            NotificationService::send(
                userId: $recipientId,
                type: 'ticket_reply',
                title: "New Reply on {$ticket->ticket_number}",
                message: "{$user->name}: " . substr(strip_tags($message), 0, 80) . '...',
                relatedType: 'ticket',
                relatedId: (string) $ticket->id,
                actionUrl: route('tickets.show', $ticket->id)
            );
        }

        return $ticketMessage;
    }

    public function updateStatus(Ticket $ticket, string $status, User $user): Ticket
    {
        $updateData = ['status' => $status];
        if (in_array($status, [Ticket::STATUS_RESOLVED, Ticket::STATUS_CLOSED])) {
            $updateData['resolved_at'] = now();
            if ($status === Ticket::STATUS_CLOSED) {
                $updateData['closed_at'] = now();
            }
        }

        $ticket->update($updateData);

        AuditService::log(
            action: 'ticket.status_changed',
            module: 'tickets',
            entityType: 'Ticket',
            entityId: (string) $ticket->id,
            entityLabel: $ticket->ticket_number,
            description: "Ticket status changed to {$status}"
        );

        return $ticket;
    }
}
