<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profession')->nullable()->after('phone');
            $table->enum('experience_level', ['entry', 'junior', 'mid', 'senior', 'lead', 'executive'])->nullable()->after('profession');
            $table->boolean('is_admin')->default(false)->after('experience_level');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['profession', 'experience_level', 'is_admin']);
        });
    }
};
