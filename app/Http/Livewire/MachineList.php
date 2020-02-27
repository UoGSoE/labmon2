<?php

namespace App\Http\Livewire;

use App\Machine;
use Livewire\Component;

class MachineList extends Component
{
    public $filter = '';

    public function render()
    {
        return view('livewire.machine-list', ['machines' => $this->getMachines()]);
    }

    public function getMachines()
    {
        if (!$this->filter) {
            return Machine::orderBy('ip')->get();
        }
        return Machine::where('ip', 'like', "%{$this->filter}%")
                ->orWhere('name', 'like', "%{$this->filter}%")
                ->orderBy('ip')
                ->get();

    }
}
