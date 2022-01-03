<?php

namespace Code16\Sharp\Auth;

class SharpEntityPolicy
{
    public function entity($user): bool
    {
        return true;
    }

    public function view($user, $instanceId): bool
    {
        return true;
    }

    public function update($user, $instanceId): bool
    {
        return true;
    }

    public function create($user): bool
    {
        return true;
    }

    public function delete($user, $instanceId)
    {
        return true;
    }
}