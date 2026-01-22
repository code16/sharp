<?php

namespace Code16\Sharp\Tests;

use Illuminate\Support\Facades\URL;

trait ResetUrlDefaults
{
    public function setUpResetUrlDefaults(): void
    {
        URL::defaults(['globalFilter' => null]);
    }
}
