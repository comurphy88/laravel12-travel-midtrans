<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bus_routes', function (Blueprint $table) {
            $table->id();
            $table->string('route_name');
            $table->string('origin');
            $table->string('destination');
            $table->string('distance', 50)->nullable();
            $table->string('duration', 50)->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->foreignId('bus_id')->constrained('buses')->cascadeOnDelete();
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bus_routes');
    }
};
