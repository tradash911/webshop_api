<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

      public function viewAny(User $user): bool
    {
        return $user->is_admin;
    }

     public function isOwner(User $user,User $model): bool
    {
        return $user->id === $model->id;

    }

   
}
