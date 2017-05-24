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

        foreach($types as $type) {
            factory(\App\Spaceship::class, 2)->create([
                "type_id" => $type->id
            ]);
        }
    }
}
