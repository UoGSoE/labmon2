<?php

namespace App\Http\Livewire;

use App\Lab;
use Livewire\Component;

class LabList extends Component
{
    public $labs;

    protected $listeners = ['labAdded' => 'refreshLabList'];

    public function render()
    {
        $this->labs = Lab::orderBy('name')->get();
        return view('livewire.lab-list', [
            'labs' => $this->labs,
        ]);
    }

    public function refreshLabList()
    {
        $this->labs = Lab::orderBy('name')->get();
    }

    public function toggleGraphable($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->is_on_graphs = ! $lab->is_on_graphs;
        $lab->save();
    }

    public function toggleLimitedRemote($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->limited_remote_access = ! $lab->limited_remote_access;
        if ($lab->limited_remote_access == true) {
            $lab->always_remote_access = false;
        }
        $lab->save();
    }

    public function toggleAlwaysRemote($id)
    {
        $lab = Lab::findOrFail($id);
        $lab->always_remote_access = ! $lab->always_remote_access;
        if ($lab->always_remote_access) {
            $lab->limited_remote_access = false;
        }
        $lab->save();
    }
}
