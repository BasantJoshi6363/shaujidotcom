<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(ReportService $reports)
    {
        $user = Auth::user();

        return view('welcome', [
            'user' => $user,
            'business' => $user->business,
            'stats' => $reports->dashboard($user->business),
        ]);
    }
}