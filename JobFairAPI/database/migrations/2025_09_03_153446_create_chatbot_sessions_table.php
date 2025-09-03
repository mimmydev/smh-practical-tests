<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('chatbot_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->string('user_identifier')->nullable(); // IP or user ID
            $table->text('user_message');
            $table->text('bot_response');
            $table->string('intent')->nullable(); // Detected intent
            $table->json('context')->nullable(); // Conversation context
            $table->float('confidence_score')->nullable();
            $table->timestamps();
            
            $table->index('session_id');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('chatbot_sessions');
    }
};