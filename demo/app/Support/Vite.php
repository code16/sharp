<?php

namespace App\Support;

class Vite extends \Illuminate\Foundation\Vite
{
    public function __invoke($entrypoints, $buildDirectory = null)
    {
        if ($buildDirectory === '/vendor/sharp') {
            $this->useHotFile(base_path('../resources/assets/dist/hot')); // allow "npm run dev" (in sharp directory)
        }

        return tap(parent::__invoke($entrypoints, $buildDirectory), function () {
            $this->useHotFile(public_path('/hot'));
        });
    }
}
