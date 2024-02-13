<?php

use Code16\Sharp\Form\Eloquent\Migrations\MigrateContentsForSharp9;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

beforeEach(function () {
    Schema::create('posts', function (Blueprint $table) {
        $table->id();
        $table->text('content');
    });
});

it('Migrate <x-sharp-image> for sharp 9', function () {
    DB::table('posts')->insert([
        'content' => '<x-sharp-image name="name.jpg" disk="local" path="data/Posts/1/name.jpg" filter-crop="0.5,0.5,0.4,0.4" filter-rotate="90"></x-sharp-image>',
    ]);
    
    $migration = new class extends Migration {
        use MigrateContentsForSharp9;
        
        public function up(): void
        {
            $this->updateContentOf(DB::table('posts')->select(['id', 'content']));
        }
    };
    
    $migration->up();
    
    $this->assertDatabaseHas('posts', [
        'content' => sprintf(
            '<x-sharp-image file="%s"></x-sharp-image>',
            e(json_encode([
                'name' => 'name.jpg',
                'disk' => 'local',
                'path' => 'data/Posts/1/name.jpg',
                'filters' => [
                    'crop' => ['x' => '0.5', 'y' => '0.5', 'width' => '0.4', 'height' => '0.4'],
                    'rotate' => ['angle' => '90']
                ]
            ])),
        ),
    ]);
});

it('Migrate <x-sharp-file> for sharp 9', function () {
    DB::table('posts')->insert([
        'content' => '<x-sharp-file name="name.pdf" disk="local" path="data/Posts/1/name.pdf"></x-sharp-file>',
    ]);
    
    $migration = new class extends Migration {
        use MigrateContentsForSharp9;
        
        public function up(): void
        {
            $this->updateContentOf(DB::table('posts')->select(['id', 'content']));
        }
    };
    
    $migration->up();
    
    $this->assertDatabaseHas('posts', [
        'content' => sprintf(
            '<x-sharp-file file="%s"></x-sharp-file>',
            e(json_encode([
                'name' => 'name.pdf',
                'disk' => 'local',
                'path' => 'data/Posts/1/name.pdf',
            ])),
        ),
    ]);
});
