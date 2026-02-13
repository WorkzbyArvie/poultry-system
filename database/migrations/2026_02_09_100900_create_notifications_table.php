<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['order', 'payment', 'product', 'promotion', 'system', 'alert']);
            $table->enum('channel', ['email', 'sms', 'in_app', 'push']);
            $table->text('data')->nullable(); // JSON: contextual data
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('external_id')->nullable(); // For tracking email/sms sends
            $table->enum('status', ['pending', 'sent', 'failed', 'bounced'])->default('pending');
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            // Performance indexes
            $table->index('user_id');
            $table->index('type');
            $table->index('channel');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
