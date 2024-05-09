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
        Schema::create('services_schedulings', function (Blueprint $table) {
            $table->id(); // Opcional
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('scheduling_id');

            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
            $table->foreign('scheduling_id')->references('id')->on('schedulings')->onDelete('cascade');

            $table->unique(['service_id', 'scheduling_id']); // Garante exclusividade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services_schedulings');
    }
};
