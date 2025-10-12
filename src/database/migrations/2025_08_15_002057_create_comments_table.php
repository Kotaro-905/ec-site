<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(false);
            $table->unsignedBigInteger('item_id')->nullable(false);
            $table->text('comment')->nullable(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('item_id')
                ->references('id')->on('items')
                ->cascadeOnUpdate()->cascadeOnDelete();

            $table->index('user_id');
            $table->index('item_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
}
