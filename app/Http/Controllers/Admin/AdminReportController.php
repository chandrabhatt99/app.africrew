<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Models\StaffingRequest;
use App\Models\WithdrawalRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminReportController extends Controller
{
    public function exportRequests(): StreamedResponse
    {
        $requests = StaffingRequest::latest()->get();
        $fileName = 'africrew_staffing_requests_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($requests) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order ID', 'Client Name', 'Email', 'Event Name', 'Category', 'Staff Count', 'Event Date', 'Location', 'Budget', 'Status']);

            foreach ($requests as $r) {
                fputcsv($handle, [
                    '#' . $r->id,
                    $r->full_name,
                    $r->email,
                    $r->event_name,
                    $r->category,
                    $r->staff_count,
                    $r->event_date ? $r->event_date->format('Y-m-d') : 'TBD',
                    $r->location,
                    '$' . number_format($r->budget ?: 250, 2),
                    strtoupper($r->status),
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }

    public function exportProfessionals(): StreamedResponse
    {
        $professionals = Professional::latest()->get();
        $fileName = 'africrew_vetted_crew_pool_' . date('Y-m-d') . '.csv';

        return response()->streamDownload(function () use ($professionals) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Full Name', 'Email', 'Phone', 'Category', 'City', 'Country', 'Experience Years', 'Day Rate', 'Status']);

            foreach ($professionals as $p) {
                fputcsv($handle, [
                    '#' . $p->id,
                    $p->full_name,
                    $p->email,
                    $p->phone,
                    $p->category,
                    $p->city,
                    $p->country,
                    $p->experience_years,
                    '$' . number_format($p->one_day_rate ?: 100, 2),
                    strtoupper($p->status),
                ]);
            }

            fclose($handle);
        }, $fileName, ['Content-Type' => 'text/csv']);
    }
}
