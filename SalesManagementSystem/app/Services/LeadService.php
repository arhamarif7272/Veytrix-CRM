<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Customer;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\Lead;
use App\Models\User;

class LeadService
{
    public function createLead(array $data, User $user): Lead
    {
        $data['created_by'] = $user->id;
        if (empty($data['assigned_to'])) {
            $data['assigned_to'] = $user->id;
        }

        $lead = Lead::create($data);

        // Log Activity
        Activity::create([
            'type'              => Activity::TYPE_CREATED,
            'subject'           => 'Lead Created',
            'description'       => "Lead '{$lead->title}' was created by {$user->name}",
            'related_type'      => 'lead',
            'related_id'        => (string) $lead->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'lead.created',
            module: 'leads',
            entityType: 'Lead',
            entityId: (string) $lead->id,
            entityLabel: $lead->title,
            newValues: $lead->toArray(),
            description: "Lead '{$lead->title}' created"
        );

        // Notify assignee if different from creator
        if ($lead->assigned_to && $lead->assigned_to !== $user->id) {
            NotificationService::send(
                userId: $lead->assigned_to,
                type: 'lead_assigned',
                title: 'New Lead Assigned',
                message: "You have been assigned a new lead: {$lead->title}",
                relatedType: 'lead',
                relatedId: (string) $lead->id,
                actionUrl: route('leads.show', $lead->id)
            );
        }

        return $lead;
    }

    public function updateLead(Lead $lead, array $data, User $user): Lead
    {
        $old = $lead->toArray();
        $oldAssigned = $lead->assigned_to;

        $lead->update($data);

        AuditService::log(
            action: 'lead.updated',
            module: 'leads',
            entityType: 'Lead',
            entityId: (string) $lead->id,
            entityLabel: $lead->title,
            oldValues: $old,
            newValues: $lead->toArray(),
            description: "Lead '{$lead->title}' updated"
        );

        if (!empty($data['assigned_to']) && $data['assigned_to'] !== $oldAssigned) {
            NotificationService::send(
                userId: $data['assigned_to'],
                type: 'lead_assigned',
                title: 'Lead Reassigned',
                message: "Lead '{$lead->title}' has been assigned to you",
                relatedType: 'lead',
                relatedId: (string) $lead->id,
                actionUrl: route('leads.show', $lead->id)
            );
        }

        return $lead;
    }

    public function assignLead(Lead $lead, string $assignedToId, User $user): Lead
    {
        $assignedUser = User::find($assignedToId);
        $oldAssigned = $lead->assigned_to;

        $lead->update(['assigned_to' => $assignedToId]);

        Activity::create([
            'type'              => Activity::TYPE_ASSIGNED,
            'subject'           => 'Lead Reassigned',
            'description'       => "Lead assigned to " . ($assignedUser?->name ?? 'User') . " by {$user->name}",
            'related_type'      => 'lead',
            'related_id'        => (string) $lead->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        if ($assignedToId !== $oldAssigned && $assignedToId !== $user->id) {
            NotificationService::send(
                userId: $assignedToId,
                type: 'lead_assigned',
                title: 'Lead Assigned',
                message: "You have been assigned to lead: {$lead->title}",
                relatedType: 'lead',
                relatedId: (string) $lead->id,
                actionUrl: route('leads.show', $lead->id)
            );
        }

        return $lead;
    }

    public function updateStatus(Lead $lead, string $status, User $user): Lead
    {
        $oldStatus = $lead->status;
        $lead->update(['status' => $status]);

        Activity::create([
            'type'              => Activity::TYPE_STATUS,
            'subject'           => 'Status Changed',
            'description'       => "Status changed from {$oldStatus} to {$status}",
            'related_type'      => 'lead',
            'related_id'        => (string) $lead->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        return $lead;
    }

    public function convertLead(Lead $lead, array $conversionData, User $user): array
    {
        // 1. Create or link Customer
        $customer = Customer::create([
            'name'           => $lead->full_name ?: ($lead->title ?: 'New Customer'),
            'email'          => $lead->email,
            'phone'          => $lead->phone,
            'company'        => $lead->company ?: ($conversionData['company_name'] ?? null),
            'source'         => $lead->source,
            'assigned_to'    => $lead->assigned_to ?: $user->id,
            'created_by'     => $user->id,
            'status'         => Customer::STATUS_ACTIVE,
            'notes'          => "Converted from lead: {$lead->title}. " . ($lead->notes ?? ''),
        ]);

        // 2. Optionally create Deal
        $deal = null;
        if (!empty($conversionData['create_deal'])) {
            $defaultStage = DealStage::orderBy('order')->first();
            $deal = Deal::create([
                'title'               => $conversionData['deal_title'] ?? ($customer->company ?: $customer->name) . ' - Initial Deal',
                'customer_id'         => (string) $customer->id,
                'lead_id'             => (string) $lead->id,
                'assigned_to'         => $customer->assigned_to,
                'created_by'          => $user->id,
                'stage_id'            => $conversionData['stage_id'] ?? ($defaultStage?->id ? (string) $defaultStage->id : 'stage_1'),
                'value'               => floatval($conversionData['deal_value'] ?? $lead->value_estimate ?? 0),
                'currency'            => $conversionData['currency'] ?? 'USD',
                'probability'         => intval($conversionData['probability'] ?? 20),
                'expected_close_date' => !empty($conversionData['expected_close_date']) ? $conversionData['expected_close_date'] : now()->addDays(30),
                'status'              => Deal::STATUS_OPEN,
            ]);
        }

        // 3. Mark Lead as Converted
        $lead->update([
            'status'       => Lead::STATUS_CONVERTED,
            'customer_id'  => (string) $customer->id,
            'deal_id'      => $deal ? (string) $deal->id : null,
            'converted_at' => now(),
        ]);

        // 4. Log Activities
        Activity::create([
            'type'              => Activity::TYPE_CONVERTED,
            'subject'           => 'Lead Converted to Customer',
            'description'       => "Lead converted into customer '{$customer->name}'" . ($deal ? " and deal '{$deal->title}'" : ""),
            'related_type'      => 'lead',
            'related_id'        => (string) $lead->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        Activity::create([
            'type'              => Activity::TYPE_CREATED,
            'subject'           => 'Customer Created via Conversion',
            'description'       => "Customer converted from lead '{$lead->title}'",
            'related_type'      => 'customer',
            'related_id'        => (string) $customer->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'lead.converted',
            module: 'leads',
            entityType: 'Lead',
            entityId: (string) $lead->id,
            entityLabel: $lead->title,
            description: "Lead '{$lead->title}' converted to Customer #{$customer->id}" . ($deal ? " and Deal #{$deal->id}" : "")
        );

        return [
            'customer' => $customer,
            'deal'     => $deal,
        ];
    }
}
