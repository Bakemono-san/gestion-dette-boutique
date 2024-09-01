<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class articlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role == \App\Enums\RoleEnum::ADMIN || $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $id): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER || $user->id == $id;
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
    public function update(User $user, $id): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $id): bool
    {
        return $user->role == \App\Enums\RoleEnum::BOUTIQUIER;
    }

}
