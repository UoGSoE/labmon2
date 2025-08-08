<?php

namespace App\Livewire\Pages;

use App\Models\Lab;
use App\Models\Machine;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
class LabShow extends Component
{
    public Lab $lab;
    
    // Lab name editing properties
    public $labName;
    public $school;
    public $editing = false;
    public $deleteButtonText = 'Delete';

    // Machine list properties
    public $filter = '';
    public $statusFilter = '';
    public $osFilter = '';
    public $includeMeta = false;

    public function mount(Lab $lab)
    {
        $this->lab = $lab;
        $this->school = $lab->school;
        $this->labName = $lab->name;
    }

    public function getTitle()
    {
        return 'Lab: ' . $this->lab->name;
    }

    public function render()
    {
        $machines = $this->getMachines();
        
        return view('livewire.pages.lab-show', [
            'machines' => $machines,
        ])->title($this->getTitle());
    }

    // Lab name editing methods
    public function updateLabName()
    {
        Lab::findOrFail($this->lab->id)->update(['name' => $this->labName, 'school' => $this->school]);
        $this->editing = false;
        $this->lab->refresh(); // Refresh the model instance
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

    // Machine list methods
    public function getMachines()
    {
        $query = $this->lab->members();
        
        if ($this->filter) {
            $query = $query->where(function($q) {
                $q->where('ip', 'like', "%{$this->filter}%")
                  ->orWhere('name', 'like', "%{$this->filter}%");
            });
        }
        
        if ($this->includeMeta && $this->filter) {
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