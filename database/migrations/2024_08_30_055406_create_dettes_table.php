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
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->float('montantTotal');
            $table->float('montantPayee');
            $table->foreignId('user_id')->constrained('users')->onDelete('set null');
            $table->foreignId('client_id')->constrained('clients')->onDelete('set null');
            $table->foreignId('article_id')->constrained('articles')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};
