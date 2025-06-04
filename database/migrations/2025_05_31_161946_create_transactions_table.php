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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_balance_id')->nullable()->constrained('balances', 'id')->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('to_balance_id')->nullable()->constrained('balances', 'id')->restrictOnDelete()->restrictOnUpdate();
            $table->string('type', 20)->index();
            $table->decimal('amount', 18, 4)->unsigned();
            $table->string('status', 20)->index();
            $table->string('comment', 255)->nullable();
            $table->fullText('comment');
            $table->timestamps();
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
