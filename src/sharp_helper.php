<?php

function sharp_version()
{
    return \Code16\Sharp\SharpServiceProvider::VERSION;
}

function sharp_user()
{
    return auth()->guard(
        config("sharp.auth.guard", config("auth.defaults.guard"))
    )->user();
}