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
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('nation_id')->nullable()->constrained('nations');
            $table->tinyInteger('is_end')->default(0);
            $table->tinyInteger('rating_qnt')->default(0);
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('highlight')->default(0);
            $table->text('url_avatar');
            $table->text('url_bg')->nullable();
            $table->string('full_name')->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->longText('desc')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->text('meta_image')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_desc')->nullable();
            $table->timestamps();
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
