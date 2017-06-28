<?php

namespace App\Sharp\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpaceshipPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the given spaceship can be updated by the user.
     *
     * @return bool
     */
    public function update(User $user, $spaceshipId)
    {
        dd("ok");
//        return $user->id === $post->user_id;
    }
}
