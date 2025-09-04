<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Exhibitor;
use App\Models\AnonymousReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CompanyTransparencyController extends Controller
{
    /**
     * Get companies with transparency scores and filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = Exhibitor::approved();

        // Search by company name
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->search . '%');
        }

        // Filter by industry
        if ($request->filled('industry')) {
            $query->byIndustry($request->industry);
        }

        // Filter by transparency score range
        if ($request->filled('min_score')) {
            $query->where('transparency_score', '>=', $request->min_score);
        }

        if ($request->filled('max_score')) {
            $query->where('transparency_score', '<=', $request->max_score);
        }

        // Add report counts for each company
        $companies = $query->withCount([
            'anonymousReports',
            'anonymousReports as high_severity_reports_count' => function ($query) {
                $query->where('severity_rating', '>=', 4);
            }
        ])->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $companies,
            'filters' => [
                'report_types' => AnonymousReport::getReportTypes(),
                'industries' => Exhibitor::distinct('industry')->pluck('industry')->filter()->values()
            ]
        ]);
    }

    /**
     * Get detailed company information with red flag reports
     */
    public function show(Exhibitor $company): JsonResponse
    {
        $company->load(['anonymousReports' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        // Group reports by type for analysis
        $reportsByType = $company->anonymousReports
            ->groupBy('report_type')
            ->map(function ($reports, $type) {
                return [
                    'type' => $type,
                    'label' => AnonymousReport::getReportTypes()[$type] ?? ucfirst(str_replace('_', ' ', $type)),
                    'count' => $reports->count(),
                    'avg_severity' => $reports->avg('severity_rating'),
                    'recent_reports' => $reports->take(3)->values()
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'company' => $company,
                'reports_summary' => [
                    'total_reports' => $company->anonymous_reports_count,
                    'high_severity_count' => $company->high_severity_reports_count,
                    'avg_severity' => $company->anonymousReports->avg('severity_rating'),
                    'by_type' => $reportsByType->values()
                ],
                'recent_reports' => $company->anonymousReports->take(10)
            ]
        ]);
    }

    /**
     * Submit anonymous red flag report
     */
    public function submitReport(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'company_name' => 'required|string|max:255',
            'report_type' => 'required|in:' . implode(',', array_keys(AnonymousReport::getReportTypes())),
            'description' => 'required|string|min:10|max:2000',
            'severity_rating' => 'required|integer|min:1|max:5'
        ]);

        $report = AnonymousReport::create($validated);

        // Update transparency score for the company if it exists as an exhibitor
        $this->updateCompanyTransparencyScore($validated['company_name']);

        return response()->json([
            'success' => true,
            'message' => 'Anonymous report submitted successfully. Thank you for helping others avoid red flags!',
            'data' => $report
        ], 201);
    }

    /**
     * Get report statistics and trends
     */
    public function statistics(): JsonResponse
    {
        $stats = [
            'total_reports' => AnonymousReport::count(),
            'companies_reported' => AnonymousReport::distinct('company_name')->count(),
            'most_reported_types' => AnonymousReport::select('report_type', DB::raw('count(*) as count'))
                ->groupBy('report_type')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get(),
            'avg_severity' => AnonymousReport::avg('severity_rating'),
            'recent_activity' => AnonymousReport::select('company_name', 'report_type', 'created_at')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Update company transparency score based on reports
     */
    private function updateCompanyTransparencyScore(string $companyName): void
    {
        // Find exact or similar company name in exhibitors
        $company = Exhibitor::where('name', 'LIKE', '%' . $companyName . '%')->first();

        if (!$company) {
            return; // Company not found in our exhibitors database
        }

        // Calculate transparency score based on reports
        $reports = AnonymousReport::forCompany($companyName)->get();
        
        if ($reports->isEmpty()) {
            return;
        }

        // Calculate score: Start with A+ (4.0) and deduct based on reports
        $baseScore = 4.0;
        $totalReports = $reports->count();
        $avgSeverity = $reports->avg('severity_rating');
        $highSeverityCount = $reports->where('severity_rating', '>=', 4)->count();

        // Scoring algorithm
        $penalty = 0;
        $penalty += ($totalReports * 0.1); // 0.1 point per report
        $penalty += ($avgSeverity - 1) * 0.2; // Additional penalty for high average severity
        $penalty += ($highSeverityCount * 0.15); // Extra penalty for high-severity reports

        $numericScore = max(0, $baseScore - $penalty);

        // Convert to letter grade
        $letterGrade = match(true) {
            $numericScore >= 3.7 => 'A+',
            $numericScore >= 3.3 => 'A',
            $numericScore >= 3.0 => 'A-',
            $numericScore >= 2.7 => 'B+',
            $numericScore >= 2.3 => 'B',
            $numericScore >= 2.0 => 'B-',
            $numericScore >= 1.7 => 'C+',
            $numericScore >= 1.3 => 'C',
            $numericScore >= 1.0 => 'C-',
            $numericScore >= 0.7 => 'D+',
            $numericScore >= 0.4 => 'D',
            default => 'F'
        };

        $company->update(['transparency_score' => $letterGrade]);
    }
}
