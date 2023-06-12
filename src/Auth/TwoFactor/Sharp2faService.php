<?php

namespace Code16\Sharp\Auth\TwoFactor;

interface Sharp2faService
{
    public function generateAndSendTokenFor($user): void;
}