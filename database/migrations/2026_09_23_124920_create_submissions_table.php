<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengisian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->references('id_subkriteria')->on('subkriteria')->cascadeOnDelete();
            $table->foreignId('user_file_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'subcategory_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengisian');
    }
};
