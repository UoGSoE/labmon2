<?php

namespace App\Http\Livewire;

use App\Lab;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class LabNameEditor extends Component
{
    public $lab;
    public $labName;
    public $editing = false;
    public $deleteButtonText = 'Delete';

    public function mount($lab)
    {
        $this->lab = $lab;
        $this->labName = $lab->name;
    }

    public function render()
    {
        return view('livewire.lab-name-editor');
    }

    public function updateLabName()
    {
        Lab::findOrFail($this->lab->id)->update(['name' => $this->labName]);
        $this->editing = false;
    }

    public function deleteLab()
    {
        if ($this->deleteButtonText === 'Delete') {
            $this->deleteButtonText = 'Confirm';

            return;
        }

        Lab::findOrFail($this->lab->id)->delete();

        return redirect('/');
    }
}
