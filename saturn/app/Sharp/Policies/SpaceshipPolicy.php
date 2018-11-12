<?php

namespace App\Sharp\Policies;

use App\User;

class SpaceshipPolicy
{

    /**
     * @param User $user
     * @return bool
     */
    public function entity(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     * @param $spaceshipId
     * @return bool
     */
    public function view(User $user, $spaceshipId)
    {
        return $spaceshipId%2 == 0 || $spaceshipId > 10;
    }

    /**
     * @param User $user
     * @param $spaceshipId
     * @return bool
     */
    public function update(User $user, $spaceshipId)
    {
        return $spaceshipId%2 == 0;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * @param User $user
     * @param $spaceshipId
     * @return bool
     */
    public function delete(User $user, $spaceshipId)
    {
        return false;
    }
}
