<?php

namespace App\Livewire\Pages;

use App\Models\Machine;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('All Machines')]
class MachineIndex extends Component
{
    // Machine list properties
    public $filter = '';
    public $statusFilter = '';
    public $osFilter = '';
    public $includeMeta = false;

    public function render()
    {
        $machines = $this->getMachines();
        
        return view('livewire.pages.machine-index', [
            'machines' => $machines,
        ]);
    }

    // Machine list methods
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
        
        if ($this->statusFilter) {
            $query = $this->mapStatusFilterToQuery($query);
        }
        
        if ($this->osFilter) {
            $query = $query->where('user_agent', 'like', "{$this->osFilter}%");
        }
        
        return $query->orderBy('ip')->get();
    }

    public function updatedFilter($value)
    {
        // This will trigger re-rendering
    }

    public function updatedIncludeMeta($value)
    {
        // This will trigger re-rendering
    }

    public function updatedStatusFilter($value)
    {
        // This will trigger re-rendering
    }

    public function updatedOsFilter($value)
    {
        // This will trigger re-rendering
    }

    public function toggleLocked($id)
    {
        $machine = Machine::findOrFail($id);
        $machine->toggleLocked();
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
    }
}