<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dateTime('start_datetime')->nullable()->after('start_date');
            $table->dateTime('end_datetime')->nullable()->after('end_date');
            $table->text('extension_rejection_reason')->nullable()->after('extension_reason');
            $table->timestamp('extension_rejected_at')->nullable()->after('extension_rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn([
                'start_datetime',
                'end_datetime',
                'extension_rejection_reason',
                'extension_rejected_at',
            ]);
        });
    }
};
