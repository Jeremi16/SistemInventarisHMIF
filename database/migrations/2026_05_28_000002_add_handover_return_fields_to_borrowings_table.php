<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            // Penyerahan
            $table->date('handover_date')->nullable()->after('admin_note');
            $table->string('handover_condition')->nullable()->after('handover_date');
            $table->string('handover_photo')->nullable()->after('handover_condition');

            // Pengembalian
            $table->date('return_date')->nullable()->after('handover_photo');
            $table->string('return_condition')->nullable()->after('return_date');
            $table->string('return_photo')->nullable()->after('return_condition');
            $table->text('damage_description')->nullable()->after('return_photo');

            // Perpanjangan (buat F-14 nanti)
            $table->boolean('extension_requested')->default(false)->after('damage_description');
            $table->date('extension_new_date')->nullable()->after('extension_requested');
            $table->text('extension_reason')->nullable()->after('extension_new_date');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn([
                'handover_date',
                'handover_condition',
                'handover_photo',
                'return_date',
                'return_condition',
                'return_photo',
                'damage_description',
                'extension_requested',
                'extension_new_date',
                'extension_reason',
            ]);
        });
    }
};
