<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class clientPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Client $client): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER || $user->role == \App\Enums\RoleEnum::CLIENT;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Client $client): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }
}
