<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_pitches', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);              // Creator or brand name
            $table->string('handle', 150);            // Instagram / TikTok handle
            $table->unsignedInteger('follower_count');
            $table->string('campaign_type', 50);
            $table->string('budget_range', 50);
            $table->text('message');
            $table->text('portfolio_links')->nullable();
            $table->string('status', 20)->default('new')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_pitches');
    }
};