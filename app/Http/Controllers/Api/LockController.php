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
        $machine = Machine::unbookedBetween($lockData['from'], $lockData['until'])->first();

        return $this->attemptLockOnMachine($machine, $lockData);
    }

    protected function labLock(array $lockData)
    {
        $lab = Lab::where('name', '=', $lockData['lock_name'])->first();

        return $this->findFreeMachineInLab($lab, $lockData);
    }

    protected function buildingLock(array $lockData)
    {
        $lab = Lab::where('name', 'like', $lockData['lock_name'] . '%')->first();

        return $this->findFreeMachineInLab($lab, $lockData);
    }

    protected function schoolLock(array $lockData)
    {
        $lab = Lab::where('school', '=', $lockData['lock_name'])->first();

        return $this->findFreeMachineInLab($lab, $lockData);
    }

    protected function findFreeMachineInLab(?Lab $lab, array $lockData)
    {
        if (! $lab) {
            return null;
        }

        $machine = $lab->members()->unbookedBetween($lockData['from'], $lockData['until'])->first();

        return $this->attemptLockOnMachine($machine, $lockData);
    }

    protected function attemptLockOnMachine(?Machine $machine, array $lockData)
    {
        if (! $machine) {
            return null;
        }

        $machine->bookings()->create([
            'guid' => $lockData['guid'],
            'start' => $lockData['from'],
            'end' => $lockData['until'],
        ]);

        return $machine;
    }
}
