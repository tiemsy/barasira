<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->index(['client_id', 'created_at'], 'missions_client_created_index');
            $table->index(['prestataire_id', 'status', 'date_start'], 'missions_provider_status_start_index');
            $table->index(['service_id', 'status', 'prestataire_id', 'date_start'], 'missions_service_status_provider_start_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->index(['user_id', 'is_active'], 'services_user_active_index');
            $table->index(['is_active', 'city_id', 'service_category_id', 'created_at'], 'services_filters_created_index');
            $table->index(['is_active', 'name'], 'services_active_name_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['mission_id', 'status', 'created_at'], 'payments_mission_status_created_index');
            $table->index(['payer_id', 'status', 'method', 'created_at'], 'payments_payer_status_method_created_index');
            $table->index(['status', 'created_at'], 'payments_status_created_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->index(['receiver_id', 'read', 'created_at'], 'messages_receiver_read_created_index');
            $table->index(['sender_id', 'receiver_id', 'mission_id', 'id'], 'messages_sender_receiver_mission_id_index');
            $table->index(['receiver_id', 'sender_id', 'mission_id', 'id'], 'messages_receiver_sender_mission_id_index');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->index(['worker_id', 'status', 'created_at'], 'applications_worker_status_created_index');
        });

        Schema::table('reviews', function (Blueprint $table) {
            $table->index(['reviewed_id', 'created_at'], 'reviews_reviewed_created_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'first_name'], 'users_role_first_name_index');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropIndex('missions_client_created_index');
            $table->dropIndex('missions_provider_status_start_index');
            $table->dropIndex('missions_service_status_provider_start_index');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropIndex('services_user_active_index');
            $table->dropIndex('services_filters_created_index');
            $table->dropIndex('services_active_name_index');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('payments_mission_status_created_index');
            $table->dropIndex('payments_payer_status_method_created_index');
            $table->dropIndex('payments_status_created_index');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex('messages_receiver_read_created_index');
            $table->dropIndex('messages_sender_receiver_mission_id_index');
            $table->dropIndex('messages_receiver_sender_mission_id_index');
        });

        Schema::table('applications', fn (Blueprint $table) => $table->dropIndex('applications_worker_status_created_index'));
        Schema::table('reviews', fn (Blueprint $table) => $table->dropIndex('reviews_reviewed_created_index'));
        Schema::table('users', fn (Blueprint $table) => $table->dropIndex('users_role_first_name_index'));
    }
};
