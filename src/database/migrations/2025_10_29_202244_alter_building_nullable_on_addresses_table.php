<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL/MariaDB 前提
        DB::statement("ALTER TABLE addresses MODIFY building VARCHAR(100) NULL");
    }

    public function down(): void
    {
        // 先に NULL を空文字に置換してから NOT NULL に戻す
        DB::statement("UPDATE addresses SET building='' WHERE building IS NULL");
        DB::statement("ALTER TABLE addresses MODIFY building VARCHAR(100) NOT NULL");
    }
};