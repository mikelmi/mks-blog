<?php

$factory->define(\Modules\Blog\Models\Post::class, function (Faker\Generator $faker) {

    static $categories;

    if (!isset($categories)) {
        $sections = \App\Models\Section::where('type', \Modules\Blog\Models\BlogCategory::class)->pluck('id')->toArray();

        $categories = $sections ?
            \Modules\Blog\Models\BlogCategory::whereIn('section_id', $sections)->pluck('id')->toArray()
            : []
        ;

        $categories[] = null;
    }

    return [
        'title' => $faker->sentence(3),
        'intro_text' => $faker->paragraph(),
        'full_text' => $faker->paragraph(5),
        'lang' => $faker->randomElement(['uk', 'en', null]),

        'category_id' => $faker->randomElement($categories),
    ];
});
