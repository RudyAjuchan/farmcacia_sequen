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
        Schema::create('producto_promociones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('productos_id')->constrained('productos');
            $table->foreignId('promociones_id')->constrained('promociones');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_promociones');
    }
};