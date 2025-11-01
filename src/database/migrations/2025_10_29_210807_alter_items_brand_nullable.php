<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE items MODIFY brand VARCHAR(255) NULL");
    }

    public function down(): void
    {
        // NULL を空文字にしてから NOT NULL へ
        DB::statement("UPDATE items SET brand='' WHERE brand IS NULL");
        DB::statement("ALTER TABLE items MODIFY brand VARCHAR(255) NOT NULL");
    }
};