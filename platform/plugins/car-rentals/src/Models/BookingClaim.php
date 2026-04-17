<?php

namespace Botble\CarRentals\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingClaim extends BaseModel
{
    public const STATUSES = [
        'open',
        'under_review',
        'awaiting_docs',
        'ready_for_decision',
        'resolved',
        'rejected',
        'closed_no_action',
    ];

    public const OUTCOME_ACTIONS = [
        'manual_only',
        'capture_deposit',
        'release_deposit',
        'partial_refund',
    ];

    protected $table = 'cr_booking_claims';

    protected $fillable = [
        'booking_id',
        'assignee_id',
        'status',
        'category',
        'claimed_amount',
        'approved_amount',
        'reason',
        'resolution_note',
        'liability_decision',
        'policy_basis',
        'evidence_completeness',
        'requires_additional_docs',
        'checklist_notes',
        'outcome_action',
        'settlement_status',
        'settlement_reference',
        'settlement_error',
        'settlement_metadata',
        'settlement_attempted_at',
        'settlement_completed_at',
        'first_response_due_at',
        'resolution_due_at',
        'priority',
        'escalated_at',
        'escalation_note',
        'evidence',
        'evidence_provenance',
        'last_notified_at',
        'resolved_at',
    ];

    protected $casts = [
        'claimed_amount' => 'float',
        'approved_amount' => 'float',
        'requires_additional_docs' => 'bool',
        'settlement_metadata' => 'array',
        'evidence_provenance' => 'array',
        'evidence' => 'array',
        'settlement_attempted_at' => 'datetime',
        'settlement_completed_at' => 'datetime',
        'first_response_due_at' => 'datetime',
        'resolution_due_at' => 'datetime',
        'escalated_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function whatsAppConversations()
    {
        return $this->hasMany(\App\Models\ChatConversation::class, 'context_id')
            ->where('context_type', 'claim')
            ->where('source', 'whatsapp');
    }

    public function whatsAppMessages()
    {
        return $this->hasManyThrough(
            \App\Models\ChatMessage::class,
            \App\Models\ChatConversation::class,
            'context_id',
            'conversation_id',
            'id',
            'id'
        )
        ->where('chat_conversations.context_type', 'claim')
        ->where('chat_messages.source', 'whatsapp');
    }
}
