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
        Schema::table('schedulings', function (Blueprint $table) {
            $table->dropForeign(['service_id']); 
            $table->dropColumn('service_id');   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedulings', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id'); // Adicionar de volta para reverter a migration
            $table->foreign('service_id')->references('id')->on('services'); // Re-criar a FK se necessário
        });
    }
};
