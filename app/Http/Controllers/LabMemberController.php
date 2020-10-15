<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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

        $ips = $this->extractValidIps($request->ips);

        $lab->replaceExistingMembers($ips);

        return redirect(route('lab.show', $lab->id));
    }

    protected function extractValidIps(string $ipList): Collection
    {
        return collect(explode("\r\n", $ipList))
        ->filter()
        ->filter(function ($ip) {
            $validator = Validator::make(['ip' => $ip], [
                'ip' => 'required|ip',
            ]);

            return $validator->passes();
        });
    }
}
