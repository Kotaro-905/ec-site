<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('item_id')
                ->references('id')->on('items')
                ->cascadeOnUpdate()->cascadeOnDelete();

            // 検索用インデックスのみ（仕様：UNIQUEなし）
            $table->index(['user_id', 'item_id']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
}
