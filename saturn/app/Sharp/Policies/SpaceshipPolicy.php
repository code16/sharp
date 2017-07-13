<?php

namespace App\Sharp\Policies;

use App\User;

class SpaceshipPolicy
{

    public function entity(User $user, string $category)
    {

    }

    /**
     * @return bool
     */
    public function view(User $user, $spaceshipId)
    {
        return $spaceshipId%2 == 0 || $spaceshipId > 10;
    }

    /**
     * @return bool
     */
    public function update(User $user, $spaceshipId)
    {
        return $spaceshipId%2 == 0;
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
