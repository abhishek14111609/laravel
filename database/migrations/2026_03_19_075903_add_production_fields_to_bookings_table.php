<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->foreignId('event_slot_id')
                ->nullable()
                ->after('event_id')
                ->constrained('event_slots')
                ->nullOnDelete();
            $table->timestamp('expires_at')->nullable()->after('payment_status');
            $table->string('qr_token')->nullable()->unique()->after('qr_code');
            $table->index('expires_at');
        });

        DB::table('bookings')
            ->where('payment_status', 'cod')
            ->update(['payment_status' => 'pending']);

        // SQLite cannot alter enum constraints directly; keep migration portable.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('pending', 'paid', 'failed') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE bookings MODIFY payment_status ENUM('pending', 'paid', 'cod', 'failed') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropIndex(['expires_at']);
            $table->dropConstrainedForeignId('event_slot_id');
            $table->dropColumn(['expires_at', 'qr_token']);
        });
    }
};
