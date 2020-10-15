<?php

namespace App\Http\Livewire;

use App\Lab;
use Illuminate\Validation\Rule;
use Livewire\Component;

class NewLabEditor extends Component
{
    public $editing = false;
    public $labName = '';

    public function saveLab()
    {
        $this->validate([
            'labName' => ['required', Rule::unique('labs', 'name')],
        ]);

        Lab::create(['name' => $this->labName]);
        $this->editing = false;
        $this->labName = '';
        $this->emit('labAdded');
    }

    public function render()
    {
        return view('livewire.new-lab-editor');
    }
}
