<?php

namespace Modules\Blog\database\seeds;


use Faker\Generator;
use Illuminate\Database\Seeder;
use Modules\Blog\Models\Post;

class BlogSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Post::truncate();
        /** @var Generator $faker */
        $faker = app(Generator::class);

        $factory = \Illuminate\Database\Eloquent\Factory::construct($faker, realpath(__DIR__ . '/../factories'));

        $factory->of(Post::class)->times(50)->create();
    }
}