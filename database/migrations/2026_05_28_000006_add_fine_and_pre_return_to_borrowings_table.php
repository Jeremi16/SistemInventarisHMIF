<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->decimal('fine_amount', 10, 2)->default(0)->after('damage_description');
            $table->string('pre_return_condition')->nullable()->after('fine_amount');
            $table->date('pre_return_check_date')->nullable()->after('pre_return_condition');
        });
    }

    public function down(): void
    {
        Schema::table('borrowings', function (Blueprint $table) {
            $table->dropColumn(['fine_amount', 'pre_return_condition', 'pre_return_check_date']);
        });
    }
};
