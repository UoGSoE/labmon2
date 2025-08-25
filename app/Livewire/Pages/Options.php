<?php

namespace App\Livewire\Pages;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Options')]
class Options extends Component
{
    #[Validate('required|numeric|min:0|max:23')]
    public $remoteStartHour;

    #[Validate('required|numeric|min:0|max:23')]
    public $remoteEndHour;

    #[Validate('nullable|string')]
    public $remoteSummer = '';

    #[Validate('nullable|string')]
    public $remoteXmas = '';

    #[Validate('nullable|string')]
    public $remoteEaster = '';

    #[Validate('nullable|string')]
    public $allowedGuids = '';

    public function mount()
    {
        $this->remoteStartHour = option('remote-start-hour', 19);
        $this->remoteEndHour = option('remote-end-hour', 6);
        $this->allowedGuids = User::allowedAccess()->pluck('username')->join("\n");
    }

    public function save()
    {
        $this->validate();

        option(['remote-start-hour' => $this->remoteStartHour]);
        option(['remote-end-hour' => $this->remoteEndHour]);

        if (! empty($this->remoteSummer)) {
            $this->extractDates($this->remoteSummer, 'summer');
        }
        if (! empty($this->remoteXmas)) {
            $this->extractDates($this->remoteXmas, 'xmas');
        }
        if (! empty($this->remoteEaster)) {
            $this->extractDates($this->remoteEaster, 'easter');
        }

        User::setAllowedUsers(auth()->user(), $this->allowedGuids);

        session()->flash('success', 'Options updated successfully!');
    }

    protected function extractDates(string $date, string $holidayName): void
    {
        $parts = explode(' ', $date);
        if (count($parts) >= 3) {
            option(["remote-start-{$holidayName}" => $parts[0]]);
            option(["remote-end-{$holidayName}" => $parts[2]]);
        }
    }

    public function render()
    {
        return view('livewire.pages.options', [
            'allowedUsers' => User::allowedAccess()->get(),
        ]);
    }
}
