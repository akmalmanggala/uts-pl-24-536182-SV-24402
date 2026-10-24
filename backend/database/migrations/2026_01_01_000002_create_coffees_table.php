<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coffees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku', 50)->unique();
            $table->string('origin', 100);
            $table->string('process', 50);
            $table->string('roast_level', 50);
            $table->unsignedInteger('price');
            $table->unsignedInteger('stock')->default(0);
            $table->string('flavor_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffees');
    }
};
