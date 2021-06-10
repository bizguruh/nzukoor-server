<?php

namespace App\Http\Controllers;

use App\Models\Contributor;
use Illuminate\Http\Request;

class ContributorController extends Controller
{
    public function index()
    {
        return Contributor::with('admin', 'user', 'facilitator')->get();
    }
}
