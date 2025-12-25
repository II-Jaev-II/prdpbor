<?php

namespace App\Livewire\Settings;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ESignature extends Component
{
    use WithFileUploads;

    public $signature;
    public $currentSignature;

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->currentSignature = $user->e_signature;
    }

    /**
     * Upload and save the e-signature.
     */
    public function saveSignature(): void
    {
        $this->validate([
            'signature' => ['required', 'image', 'max:2048'], // 2MB max
        ]);

        /** @var User $user */
        $user = Auth::user();

        // Delete old signature if exists
        if ($user->e_signature) {
            Storage::disk('public')->delete($user->e_signature);
        }

        // Store new signature
        $path = $this->signature->store('signatures', 'public');

        // Update user
        $user->e_signature = $path;
        $user->save();

        // Update current signature
        $this->currentSignature = $path;

        // Reset file input
        $this->reset('signature');

        session()->flash('status', 'signature-updated');
    }

    /**
     * Delete the e-signature.
     */
    public function deleteSignature(): void
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user->e_signature) {
            Storage::disk('public')->delete($user->e_signature);
            $user->e_signature = null;
            $user->save();

            $this->currentSignature = null;

            session()->flash('status', 'signature-deleted');
        }
    }

    public function render()
    {
        return view('livewire.settings.e-signature');
    }
}
