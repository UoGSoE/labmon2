<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Lab;
use App\Models\Machine;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

class LockController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'guid' => 'required|string|max:60',
            'from' => 'required|date_format:Y-m-d H:i',
            'until' => 'required|date_format:Y-m-d H:i',
            'lock_type' => 'required|string|max:60|in:any,lab,school,building',
            'lock_name' => 'nullable|string',
        ]);

        $lockedMachine = $this->lockMachine($data);

        if (! $lockedMachine) {
            return response()->json([
                'message' => 'No machines available',
                'success' => false,
                'data' => null,
            ], Response::HTTP_CONFLICT);
        }

        return response()->json([
            'success' => true,
            'message' => 'Machine locked',
            'data' => $lockedMachine,
        ], 201);
    }

    protected function lockMachine(array $lockData)
    {
        $lockData['from'] = Carbon::createFromFormat('Y-m-d H:i', $lockData['from']);
        $lockData['until'] = Carbon::createFromFormat('Y-m-d H:i', $lockData['until']);
        return match ($lockData['lock_type']) {
            'school' => $this->schoolLock($lockData),
            'building' => $this->buildingLock($lockData),
            'lab' => $this->labLock($lockData),
            default => $this->anyLock($lockData),
        };
    }

    public function anyLock(array $lockData)
    {
        $machine = Machine::unlockedBetween($lockData['from'], $lockData['until'])->first();

        if (! $machine) {
            return null;
        }

        $machine->lockFor($lockData['guid'], $lockData['from'], $lockData['until']);

        return $machine;
    }

    protected function labLock(array $lockData)
    {
        $lab = Lab::where('name', '=', $lockData['lock_name'])->first();
        if (! $lab) {
            return null;
        }

        $machine = $lab->members()->unlockedBetween($lockData['from'], $lockData['until'])->first();
        if (! $machine) {
            return null;
        }

        $machine->lockFor($lockData['guid'], $lockData['from'], $lockData['until']);

        return $machine;
    }
}
