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
        Schema::create('article_dettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dette_id')->constrained('dettes')->onDelete('cascade');
            $table->foreignId('article_id')->nullable()->constrained('articles')->onDelete('set null');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_dettes');
    }
};
