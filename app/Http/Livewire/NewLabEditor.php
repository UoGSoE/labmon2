<?php

namespace App\Http\Livewire;

use App\Models\Lab;
use Illuminate\Validation\Rule;
use Livewire\Component;

class NewLabEditor extends Component
{
    public $editing = false;

    public $labName = '';

    public $school = '';

    public function saveLab()
    {
        $this->validate([
            'labName' => ['required', Rule::unique('labs', 'name')],
            'school' => ['required'],
        ]);

        Lab::create(['name' => $this->labName, 'school' => $this->school]);
        $this->editing = false;
        $this->labName = '';
        $this->school = '';
        $this->emit('labAdded');
    }

    public function render()
    {
        return view('livewire.new-lab-editor');
    }
}
