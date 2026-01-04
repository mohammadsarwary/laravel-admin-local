<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->boolean('is_read_by_buyer')->default(true);
            $table->boolean('is_read_by_seller')->default(false);
            $table->timestamps();

            $table->unique(['ad_id', 'buyer_id', 'seller_id']);
            $table->index('buyer_id');
            $table->index('seller_id');
            $table->index('ad_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};
