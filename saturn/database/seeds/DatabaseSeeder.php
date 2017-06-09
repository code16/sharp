<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = factory(\App\SpaceshipType::class, 10)->create();

        $pilots = factory(\App\Pilot::class, 10)->create();

        foreach($types as $type) {
            $spaceships = factory(\App\Spaceship::class, 2)->create([
                "type_id" => $type->id
            ]);

            foreach($spaceships as $spaceship) {
                factory(\App\TechnicalReview::class, rand(1, 2))->create([
                    "spaceship_id" => $spaceship->id
                ]);

                $spaceship->pilots()->sync(
                    $pilots->random(rand(1, 3))->pluck("id")->all()
                );
            }
        }
    }
}
