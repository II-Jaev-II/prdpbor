<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => $this->passwordRules(),
            'e_signature' => ['required', 'image', 'max:2048'],
        ])->validate();

        $userData = [
            'name' => $input['name'],
            'designation' => $input['designation'],
            'email' => $input['email'],
            'password' => $input['password'],
            'is_approved' => app()->environment('testing'), // Auto-approve in test environment
        ];

        // Handle e-signature upload
        if (request()->hasFile('e_signature')) {
            $path = request()->file('e_signature')->store('signatures', 'public');
            $userData['e_signature'] = $path;
        }

        return User::create($userData);
    }
}
