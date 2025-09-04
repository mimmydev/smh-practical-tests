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
        // Rename table from contact_submissions to anonymous_reports
        Schema::rename('contact_submissions', 'anonymous_reports');

        // Transform the table structure
        Schema::table('anonymous_reports', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['name', 'email', 'phone', 'subject', 'category', 'status', 'admin_notes', 'resolved_at']);
            
            // Add new columns for red flag reports
            $table->string('company_name')->after('id');
            $table->enum('report_type', [
                'salary_issues', 
                'toxic_management', 
                'false_promises', 
                'poor_benefits', 
                'unpaid_overtime', 
                'discrimination', 
                'unsafe_conditions',
                'other'
            ])->after('company_name');
            $table->renameColumn('message', 'description');
            $table->integer('severity_rating')->default(3)->after('description'); // 1-5 scale
            
            // Add index for searching
            $table->index(['company_name', 'report_type']);
            $table->index('severity_rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the transformation
        Schema::table('anonymous_reports', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['company_name', 'report_type', 'severity_rating']);
            $table->dropIndex(['company_name', 'report_type']);
            $table->dropIndex(['severity_rating']);
            
            // Add back old columns
            $table->string('name')->after('id');
            $table->string('email')->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->string('subject')->after('phone');
            $table->renameColumn('description', 'message');
            $table->enum('category', ['general', 'exhibitor', 'visitor', 'technical'])->default('general')->after('message');
            $table->enum('status', ['new', 'in_progress', 'resolved'])->default('new')->after('category');
            $table->text('admin_notes')->nullable()->after('status');
            $table->timestamp('resolved_at')->nullable()->after('admin_notes');
            
            // Add back old indexes
            $table->index(['status', 'category']);
        });
        
        // Rename back to original table name
        Schema::rename('anonymous_reports', 'contact_submissions');
    }
};
