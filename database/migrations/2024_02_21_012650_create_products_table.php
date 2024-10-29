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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_year')->nullable()->constrained('years');
            $table->foreignId('id_nation')->nullable()->constrained('nations');
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('highlight')->default(0);
            $table->integer('is_18')->default(value: 1);
            $table->integer('ord')->default(0);
            $table->text('url_avatar');
            $table->text('url_bg')->nullable();
            $table->string('full_name')->nullable();
            $table->string('date')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('desc')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->text('meta_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
