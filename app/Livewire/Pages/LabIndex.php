<?php

namespace App\Livewire\Pages;

use App\Models\Lab;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Labs')]
class LabIndex extends Component
{
    // New lab creation properties
    public $editing = false;

    public $labName = '';

    public $school = '';

    public function render()
    {
        $labs = Lab::withCount([
            'members',
            'members as online_count' => fn ($query) => $query->online(),
        ])->orderBy('name')->get();

        return view('livewire.pages.lab-index', [
            'labs' => $labs,
        ]);
    }

    // New lab creation methods
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
        $this->dispatch('refreshLabs');
    }

    // Lab management methods
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
