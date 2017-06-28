<?php

namespace App\Sharp\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpaceshipPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function view(User $user, $spaceshipId)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function update(User $user, $spaceshipId)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * @return bool
     */
    public function delete(User $user, $spaceshipId)
    {
        return false;
    }
}
