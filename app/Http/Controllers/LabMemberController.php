<?php

namespace App\Http\Controllers;

use App\Lab;
use App\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $request->validate([
            'ips' => 'required',
        ]);

        $ips = collect(explode("\r\n", $request->ips))
                ->filter()
                ->filter(function ($ip) use ($request) {
                    $validator = Validator::make(['ip' => $ip], [
                        'ip' => 'required|ip',
                    ]);

                    return $validator->passes();
                });
        $lab->members->each(function ($machine) {
            $machine->update(['lab_id' => null]);
        });

        $ips->each(function ($ip) use ($lab) {
            $machine = Machine::firstOrCreate([
                'ip' => $ip,
            ]);
            $machine->update(['lab_id' => $lab->id]);
        });

        return redirect(route('lab.show', $lab->id));
    }
}
