<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', ['user' => Auth::user()]);
    }

    public function postingProcedure()
    {
        return view('posting-procedure');
    }

    public function dataGathering()
    {
        return view('data-gathering');
    }

    public function ecommerceRequirements()
    {
        return view('ecommerce-requirements');
    }

    public function priceCalculator()
    {
        return view('price-calculator');
    }

    public function endOfDay()
    {
        return view('end-of-day');
    }

    public function importantLinks()
    {
        return view('important-links');
    }

    public function team()
    {
        $managers = User::where('role', 'manager')->get();
        $leads = User::where('role', 'lead')->get();
        $content = User::where('role', 'content')->get();
        $graphics = User::where('role', 'graphics')->get();

        return view('team', compact('managers', 'leads', 'content', 'graphics'));
    }
}
