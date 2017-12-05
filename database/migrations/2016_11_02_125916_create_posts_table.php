<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');

            $table->string('title');
            $table->string('slug');
            $table->text('intro_text');
            $table->mediumText('full_text');

            $table->string('lang',10)->nullable()->default(null);

            $table->string('meta_title')->nullable()->default(null);
            $table->string('meta_keywords')->nullable()->default(null);
            $table->text('meta_description')->nullable()->default(null);

            $table->unsignedInteger('category_id')->nullable()->default(null);
            $table->json('params')->nullable();
            $table->boolean('featured')->default(false);
            $table->string('image')->nullable()->default(null);
            $table->unsignedInteger('hits')->default(0);
            $table->boolean('status')->default(1);

            $table->timestamps();
            $table->softDeletes();
            \App\Traits\Authority::addColumns($table);

            $table->foreign('category_id')->references('id')->on('categories')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
