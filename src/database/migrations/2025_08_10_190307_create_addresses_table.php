<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $t) {
            $t->id();
            $t->foreignId('purchase_id')->nullable()->constrained()->cascadeOnDelete();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->string('address', 255);
            $t->string('postal_code', 20);
            $t->string('building', 100)->nullable();
            $t->timestamps();

            $t->index('user_id');
            $t->index('purchase_id');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
