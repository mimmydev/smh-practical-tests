<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exhibitor_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description');
            $table->text('requirements');
            $table->string('salary_range')->nullable();
            $table->enum('job_type', ['full_time', 'part_time', 'contract', 'internship']);
            $table->string('location');
            $table->json('skills_required')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('available_slots')->default(10);
            $table->timestamps();
            
            $table->index(['is_active', 'job_type']);
            $table->index('exhibitor_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jobs');
    }
};