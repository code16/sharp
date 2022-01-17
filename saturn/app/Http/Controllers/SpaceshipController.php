<?php

namespace App\Http\Controllers;

use App\Spaceship;

class SpaceshipController extends Controller
{
    public function show(Spaceship $spaceship)
    {
        return view('pages.spaceships.spaceship', [
            'spaceship' => $spaceship,
        ]);
    }
}
