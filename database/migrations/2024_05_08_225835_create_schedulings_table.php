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
        Schema::create('schedulings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false);
            $table->string('whats', 15)->nullable(false);
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('status');
            
            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('service_id'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedulings');
    }
};
