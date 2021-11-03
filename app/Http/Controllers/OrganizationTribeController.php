<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use Illuminate\Http\Request;

class OrganizationTribeController extends Controller
{
    public function index()
    {
        return  Tribe::paginate(15);
    }

    public function store()
    {
    }

    public function show(Tribe $Tribe)
    {
    }

    public function destroy(Tribe $Tribe)
    {
    }
}
