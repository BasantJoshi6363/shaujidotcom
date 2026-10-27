<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index(ReportService $reports)
    {
        $user = Auth::user();
        $business = $user->business;
        $stats = $reports->dashboard($business);

        return view('protect.profile', [
            'user' => $user,
            'business' => $business,
            'customerCount' => $stats['customer_count'],
            'outstanding' => $stats['total_outstanding'],
        ]);
    }
}