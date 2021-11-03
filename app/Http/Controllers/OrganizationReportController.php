<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class OrganizationReportController extends Controller
{
    public function index()
    {
        return Report::with('user')->paginate(15);
    }

    public function store()
    {
    }

    public function show(Report $report)
    {
        return $report->load('user');
    }

    public function destroy(Report $report)
    {
    }
}
