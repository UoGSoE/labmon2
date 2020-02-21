<?php

namespace App\Http\Controllers;

use App\Lab;
use Illuminate\Http\Request;

class LabMemberController extends Controller
{
    public function edit(Lab $lab)
    {
        return view('lab.members.edit', [
            'lab' => $lab,
        ]);
    }

    public function update(Lab $lab, Request $request)
    {
        dd($request->ips);
    }
}
