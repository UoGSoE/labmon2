<?php

namespace App\Http\Livewire;

use App\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public $machines;

    public $labId;

    public $filter = '';

    public $includeMeta = false;

    public function mount($machines = null, $labId = null)
    {
        if (! $machines) {
            $machines = collect([]);
        }
        $this->machines = $machines;
        $this->labId = $labId;
    }

    public function render()
    {
        return view('livewire.machine-list');
    }

    public function updatedFilter($value)
    {
        $this->getMachines();
    }

    public function updatedIncludeMeta($value)
    {
        $this->getMachines();
    }

    public function getMachines()
    {
        \Log::info('111');
        if (! $this->filter && ! $this->labId) {
            $this->machines = Machine::orderBy('ip')->get();
            return;
        }
        \Log::info('222');
        if (! $this->filter) {
            if ($this->labId) {
                \Log::info('333');
                $this->machines = Machine::where('lab_id', '=', $this->labId)->orderBy('ip')->get();
                return;
            }
            \Log::info('444');

            $this->machines = Machine::orderBy('ip')->get();
            return;
        }
        \Log::info('555');

        $query = Machine::where('ip', 'like', "%{$this->filter}%")
                ->orWhere('name', 'like', "%{$this->filter}%");
        if ($this->includeMeta) {
            $query = $query->orWhere('meta', 'like', "%{$this->filter}%");
        }
        if ($this->labId) {
            $query = $query->where('lab_id', '=', $this->labId);
        }
        $this->machines = $query->orderBy('ip')->get();
    }

    public function toggleLocked($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->toggleLocked();
        $this->getMachines();
    }
}
