<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings_sets', function (Blueprint $table) {
            $table->id();
            $table->string('open_router_llm_model');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings_sets');
    }
};
