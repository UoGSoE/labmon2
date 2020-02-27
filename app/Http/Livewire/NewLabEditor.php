<?php

namespace App\Http\Livewire;

use App\Lab;
use Livewire\Component;
use Illuminate\Validation\Rule;

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
