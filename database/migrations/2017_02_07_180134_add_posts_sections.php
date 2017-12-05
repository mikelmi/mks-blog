<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPostsSections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->unsignedInteger('section_id')->nullable()->default(null);
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('posts', 'section_id')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->dropColumn('section_id');
            });
        }
    }
}
