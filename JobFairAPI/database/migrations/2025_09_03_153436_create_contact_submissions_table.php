<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('subject');
            $table->text('message');
            $table->enum('category', ['general', 'exhibitor', 'visitor', 'technical'])->default('general');
            $table->enum('status', ['new', 'in_progress', 'resolved'])->default('new');
            $table->text('admin_notes')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'category']);
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('contact_submissions');
    }
};