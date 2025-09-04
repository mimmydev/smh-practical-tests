<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnonymousReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'report_type',
        'description',
        'severity_rating'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'severity_rating' => 'integer'
    ];

    /**
     * Get the available report types
     */
    public static function getReportTypes(): array
    {
        return [
            'salary_issues' => 'Salary Issues',
            'toxic_management' => 'Toxic Management',
            'false_promises' => 'False Promises',
            'poor_benefits' => 'Poor Benefits',
            'unpaid_overtime' => 'Unpaid Overtime',
            'discrimination' => 'Discrimination',
            'unsafe_conditions' => 'Unsafe Conditions',
            'other' => 'Other'
        ];
    }

    /**
     * Scope to get reports for a specific company
     */
    public function scopeForCompany($query, $companyName)
    {
        return $query->where('company_name', 'LIKE', '%' . $companyName . '%');
    }

    /**
     * Scope to get reports by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    /**
     * Scope to get reports by minimum severity
     */
    public function scopeBySeverity($query, $minSeverity)
    {
        return $query->where('severity_rating', '>=', $minSeverity);
    }

    /**
     * Get report type label
     */
    public function getReportTypeLabelAttribute()
    {
        return self::getReportTypes()[$this->report_type] ?? ucfirst(str_replace('_', ' ', $this->report_type));
    }
}
