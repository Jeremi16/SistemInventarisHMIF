<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE borrowings MODIFY start_date DATETIME NOT NULL, MODIFY end_date DATETIME NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE borrowings ALTER COLUMN start_date TYPE timestamp(0) without time zone USING start_date::timestamp(0) without time zone');
            DB::statement('ALTER TABLE borrowings ALTER COLUMN end_date TYPE timestamp(0) without time zone USING end_date::timestamp(0) without time zone');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE borrowings ALTER COLUMN start_date datetime NOT NULL');
            DB::statement('ALTER TABLE borrowings ALTER COLUMN end_date datetime NOT NULL');
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE borrowings MODIFY start_date DATE NOT NULL, MODIFY end_date DATE NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE borrowings ALTER COLUMN start_date TYPE date USING start_date::date');
            DB::statement('ALTER TABLE borrowings ALTER COLUMN end_date TYPE date USING end_date::date');
        } elseif ($driver === 'sqlsrv') {
            DB::statement('ALTER TABLE borrowings ALTER COLUMN start_date date NOT NULL');
            DB::statement('ALTER TABLE borrowings ALTER COLUMN end_date date NOT NULL');
        }
    }
};
