<?php

namespace App\Http\Controllers\P3pot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('p3pot.customer.dashboard');
    }
}
