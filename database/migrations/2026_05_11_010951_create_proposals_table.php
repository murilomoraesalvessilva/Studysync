<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('proposer_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['task_swap', 'topic_split', 'overload', 'deadline_extension']);
            $table->text('description');
            $table->enum('status', ['open', 'approved', 'rejected', 'expired'])->default('open');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proposals');
    }
};
