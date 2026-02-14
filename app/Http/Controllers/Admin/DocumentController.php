<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentRequest;
use App\Models\DocumentSubmission;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function index(): View
    {
        $now = Carbon::now();
        $expiringCutoff = $now->copy()->addDays(14);

        $requests = DocumentRequest::query()->withCount('submissions');

        $totalRequests = (clone $requests)->count();
        $pendingReviewCount = (clone $requests)->has('submissions')->count();
        $expiringSoonCount = (clone $requests)
            ->whereBetween('due_at', [$now, $expiringCutoff])
            ->doesntHave('submissions')
            ->count();

        $recentSubmissions = DocumentSubmission::query()
            ->with([
                'user:id,name',
                'documentRequest:id,title,due_at',
            ])
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $approvedCount = 0;
        $rejectedCount = 0;

        return view('admin.documents.index', [
            'totalRequests' => $totalRequests,
            'pendingReviewCount' => $pendingReviewCount,
            'expiringSoonCount' => $expiringSoonCount,
            'approvedCount' => $approvedCount,
            'rejectedCount' => $rejectedCount,
            'recentSubmissions' => $recentSubmissions,
        ]);
    }
}
