<?php

namespace Code16\Sharp\Auth;

interface SharpAuthCheck
{

    /**
     * Check if $user is allowed to use Sharp
     *
     * @param $user
     * @return bool
     */
    function allowUserInSharp($user): bool;
}