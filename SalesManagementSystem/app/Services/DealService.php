<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\Deal;
use App\Models\DealStage;
use App\Models\User;

class DealService
{
    public function createDeal(array $data, User $user): Deal
    {
        $data['created_by'] = $user->id;
        if (empty($data['assigned_to'])) {
            $data['assigned_to'] = $user->id;
        }
        if (empty($data['status'])) {
            $data['status'] = Deal::STATUS_OPEN;
        }

        $deal = Deal::create($data);

        Activity::create([
            'type'              => Activity::TYPE_CREATED,
            'subject'           => 'Deal Created',
            'description'       => "Deal '{$deal->title}' was created",
            'related_type'      => 'deal',
            'related_id'        => (string) $deal->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        AuditService::log(
            action: 'deal.created',
            module: 'deals',
            entityType: 'Deal',
            entityId: (string) $deal->id,
            entityLabel: $deal->title,
            newValues: $deal->toArray(),
            description: "Deal '{$deal->title}' created"
        );

        if ($deal->assigned_to && $deal->assigned_to !== $user->id) {
            NotificationService::send(
                userId: $deal->assigned_to,
                type: 'deal_assigned',
                title: 'New Deal Assigned',
                message: "You have been assigned to deal: {$deal->title}",
                relatedType: 'deal',
                relatedId: (string) $deal->id,
                actionUrl: route('deals.show', $deal->id)
            );
        }

        return $deal;
    }

    public function updateDeal(Deal $deal, array $data, User $user): Deal
    {
        $old = $deal->toArray();
        $deal->update($data);

        AuditService::log(
            action: 'deal.updated',
            module: 'deals',
            entityType: 'Deal',
            entityId: (string) $deal->id,
            entityLabel: $deal->title,
            oldValues: $old,
            newValues: $deal->toArray(),
            description: "Deal '{$deal->title}' updated"
        );

        return $deal;
    }

    public function updateStage(Deal $deal, string $stageId, User $user): Deal
    {
        $oldStageId = $deal->stage_id;
        $newStage = DealStage::find($stageId);

        $updateData = ['stage_id' => $stageId];

        // If moved to won stage
        if ($newStage && $newStage->is_won) {
            $updateData['status'] = Deal::STATUS_WON;
            $updateData['actual_close_date'] = now();
            $updateData['probability'] = 100;
        } elseif ($newStage && $newStage->is_lost) {
            $updateData['status'] = Deal::STATUS_LOST;
            $updateData['actual_close_date'] = now();
            $updateData['probability'] = 0;
        } else {
            $updateData['status'] = Deal::STATUS_OPEN;
        }

        $deal->update($updateData);

        Activity::create([
            'type'              => Activity::TYPE_STATUS,
            'subject'           => 'Deal Stage Changed',
            'description'       => "Stage changed to " . ($newStage?->name ?? $stageId),
            'related_type'      => 'deal',
            'related_id'        => (string) $deal->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        return $deal;
    }

    public function markWon(Deal $deal, User $user): Deal
    {
        $wonStage = DealStage::where('is_won', true)->first();

        $deal->update([
            'status'            => Deal::STATUS_WON,
            'stage_id'          => $wonStage ? (string) $wonStage->id : $deal->stage_id,
            'actual_close_date' => now(),
            'probability'       => 100,
        ]);

        Activity::create([
            'type'              => Activity::TYPE_STATUS,
            'subject'           => 'Deal Won',
            'description'       => "Deal '{$deal->title}' marked as WON!",
            'related_type'      => 'deal',
            'related_id'        => (string) $deal->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        if ($deal->assigned_to) {
            NotificationService::send(
                userId: $deal->assigned_to,
                type: 'deal_won',
                title: 'Deal Won! 🎉',
                message: "Deal '{$deal->title}' with value \${$deal->value} was marked as WON",
                relatedType: 'deal',
                relatedId: (string) $deal->id,
                actionUrl: route('deals.show', $deal->id)
            );
        }

        return $deal;
    }

    public function markLost(Deal $deal, string $reason, User $user): Deal
    {
        $lostStage = DealStage::where('is_lost', true)->first();

        $deal->update([
            'status'            => Deal::STATUS_LOST,
            'stage_id'          => $lostStage ? (string) $lostStage->id : $deal->stage_id,
            'lost_reason'       => $reason,
            'actual_close_date' => now(),
            'probability'       => 0,
        ]);

        Activity::create([
            'type'              => Activity::TYPE_STATUS,
            'subject'           => 'Deal Lost',
            'description'       => "Deal '{$deal->title}' marked as LOST: {$reason}",
            'related_type'      => 'deal',
            'related_id'        => (string) $deal->id,
            'performed_by'      => $user->id,
            'performed_by_name' => $user->name,
            'occurred_at'       => now(),
        ]);

        return $deal;
    }

    public function getPipelineData(?string $assignedTo = null): array
    {
        $stages = DealStage::orderBy('order')->get();
        $pipeline = [];

        foreach ($stages as $stage) {
            $query = Deal::where('stage_id', (string) $stage->id)->where('status', Deal::STATUS_OPEN);
            if ($assignedTo) {
                $query->where('assigned_to', $assignedTo);
            }

            $deals = $query->get();
            $pipeline[] = [
                'stage'       => $stage,
                'deals'       => $deals,
                'count'       => $deals->count(),
                'total_value' => $deals->sum('value'),
            ];
        }

        return $pipeline;
    }
}
