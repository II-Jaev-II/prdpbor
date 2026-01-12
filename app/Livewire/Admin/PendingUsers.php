<?php

namespace App\Livewire\Admin;

use App\Enum\UnitComponent;
use App\Models\User;
use Livewire\Component;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class PendingUsers extends Component
{
    use AuthorizesRequests;

    public $showAssignModal = false;
    public $selectedUserId = null;
    public $selectedUser = null;
    public $selectedUnitComponent = '';

    protected $listeners = ['openAssignModal'];

    public function mount()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        if ($user->unit_component !== 'ADMIN') {
            abort(403, 'Unauthorized access.');
        }
    }

    public function openAssignModal($userId)
    {
        $this->selectedUserId = $userId;
        $this->selectedUser = User::find($userId);
        $this->selectedUnitComponent = '';
        $this->showAssignModal = true;
    }

    public function assignUnitComponent()
    {
        $this->validate([
            'selectedUnitComponent' => 'required|in:' . implode(',', UnitComponent::values()),
        ]);

        $user = User::find($this->selectedUserId);
        if ($user) {
            $user->update([
                'unit_component' => $this->selectedUnitComponent,
                'is_approved' => true,
            ]);

            $this->dispatch('userAssigned');
            $this->dispatch('$refresh');
            $this->closeModal();
            
            session()->flash('success', 'Unit component assigned successfully to ' . $user->name);
        }
    }

    public function closeModal()
    {
        $this->showAssignModal = false;
        $this->selectedUserId = null;
        $this->selectedUser = null;
        $this->selectedUnitComponent = '';
    }

    public function render()
    {
        return view('livewire.admin.pending-users');
    }
}
