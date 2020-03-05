<?php

namespace App\Http\Livewire;

use App\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public $filter = '';

    public $includeMeta = false;

    public function render()
    {
        return view('livewire.machine-list', ['machines' => $this->getMachines()]);
    }

    public function getMachines()
    {
        if (!$this->filter) {
            return Machine::orderBy('ip')->get();
        }
        $query = Machine::where('ip', 'like', "%{$this->filter}%")
                ->orWhere('name', 'like', "%{$this->filter}%");
        if ($this->includeMeta) {
            $query = $query->orWhere('meta', 'like', "%{$this->filter}%");
        }

        return $query->orderBy('ip')->get();

    }
}
