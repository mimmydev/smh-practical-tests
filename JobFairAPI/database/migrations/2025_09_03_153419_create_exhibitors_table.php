<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exhibitors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('contact_email')->unique();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('industry')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('booth_preferences')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('industry');
        });
    }

    public function down()
    {
        Schema::dropIfExists('exhibitors');
    }
};