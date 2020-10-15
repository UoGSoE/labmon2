<?php

namespace App\Http\Livewire;

use App\Models\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public $machines;

    public $labId;

    public $filter = '';

    public $statusFilter = '';

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

    public function updatedStatusFilter($value)
    {
        $this->getMachines();
    }

    public function getMachines()
    {
        $query = Machine::query();
        if ($this->filter) {
            $query = $query->where('ip', 'like', "%{$this->filter}%")
                ->orWhere('name', 'like', "%{$this->filter}%");
        }
        if ($this->includeMeta) {
            $query = $query->orWhere('meta', 'like', "%{$this->filter}%");
        }
        if ($this->labId) {
            $query = $query->where('lab_id', '=', $this->labId);
        }
        if ($this->statusFilter) {
            $query = $this->mapStatusFilterToQuery($query);
        }
        $this->machines = $query->orderBy('ip')->get();
    }

    public function toggleLocked($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->toggleLocked();
        $this->getMachines();
    }

    protected function mapStatusFilterToQuery($query)
    {
        switch ($this->statusFilter) {
            case 'logged_in':
                return $query->where('logged_in', '=', true);
            case 'not_logged_in':
                return $query->where('logged_in', '=', false);
            case 'locked':
                return $query->where('is_locked', '=', true);
            case 'not_locked':
                return $query->where('is_locked', '=', false);
            default:
                return $query;
        }

        return $query;
    }
}
