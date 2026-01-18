<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Contract; 

class CustomerDashboardController extends Controller
{
    public function index()
    {
        $contracts = Contract::where(
            'customer_id',
            auth()->user()->customer->id
        )->get();

       return view('customer.dashboard', compact('contracts'));

    }
}
