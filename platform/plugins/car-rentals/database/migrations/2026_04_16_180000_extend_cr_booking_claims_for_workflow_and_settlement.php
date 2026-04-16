<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('cr_booking_claims')) {
            return;
        }

        Schema::table('cr_booking_claims', function (Blueprint $table): void {
            if (! Schema::hasColumn('cr_booking_claims', 'liability_decision')) {
                $table->string('liability_decision', 30)->nullable()->after('resolution_note');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'policy_basis')) {
                $table->text('policy_basis')->nullable()->after('liability_decision');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'evidence_completeness')) {
                $table->string('evidence_completeness', 30)->default('partial')->after('policy_basis');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'requires_additional_docs')) {
                $table->boolean('requires_additional_docs')->default(false)->after('evidence_completeness');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'checklist_notes')) {
                $table->text('checklist_notes')->nullable()->after('requires_additional_docs');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'outcome_action')) {
                $table->string('outcome_action', 30)->default('manual_only')->after('checklist_notes');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_status')) {
                $table->string('settlement_status', 30)->default('pending')->after('outcome_action');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_reference')) {
                $table->string('settlement_reference', 120)->nullable()->after('settlement_status');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_error')) {
                $table->text('settlement_error')->nullable()->after('settlement_reference');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_metadata')) {
                $table->json('settlement_metadata')->nullable()->after('settlement_error');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_attempted_at')) {
                $table->dateTime('settlement_attempted_at')->nullable()->after('settlement_metadata');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'settlement_completed_at')) {
                $table->dateTime('settlement_completed_at')->nullable()->after('settlement_attempted_at');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'first_response_due_at')) {
                $table->dateTime('first_response_due_at')->nullable()->after('settlement_completed_at');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'resolution_due_at')) {
                $table->dateTime('resolution_due_at')->nullable()->after('first_response_due_at');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'priority')) {
                $table->string('priority', 20)->default('normal')->after('resolution_due_at');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'escalated_at')) {
                $table->dateTime('escalated_at')->nullable()->after('priority');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'escalation_note')) {
                $table->text('escalation_note')->nullable()->after('escalated_at');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'evidence_provenance')) {
                $table->json('evidence_provenance')->nullable()->after('evidence');
            }
            if (! Schema::hasColumn('cr_booking_claims', 'last_notified_at')) {
                $table->dateTime('last_notified_at')->nullable()->after('evidence_provenance');
            }
        });

        Schema::table('cr_booking_claims', function (Blueprint $table): void {
            $table->index(['status', 'priority'], 'cr_booking_claims_status_priority_idx');
            $table->index(['resolution_due_at', 'status'], 'cr_booking_claims_resolution_due_status_idx');
            $table->index(['escalated_at', 'status'], 'cr_booking_claims_escalated_status_idx');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('cr_booking_claims')) {
            return;
        }

        Schema::table('cr_booking_claims', function (Blueprint $table): void {
            foreach ([
                'cr_booking_claims_status_priority_idx',
                'cr_booking_claims_resolution_due_status_idx',
                'cr_booking_claims_escalated_status_idx',
            ] as $indexName) {
                try {
                    $table->dropIndex($indexName);
                } catch (\Throwable) {
                }
            }
        });
    }
};
