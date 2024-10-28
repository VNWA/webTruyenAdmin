<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('episodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_product')->constrained('products')->onDelete('cascade');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->unique(['id_product', 'slug']);

        });
    }


    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
