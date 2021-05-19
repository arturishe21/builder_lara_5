<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->increments('id');
            $table->string('type');
            $table->string('title');
            $table->string('slug');
            $table->text('value');
            $table->string('group');
            $table->json('value_languages');
            $table->string('file');
            $table->tinyInteger('check');
            $table->json('textarea_with_languages');
            $table->json('froala_with_languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('settings');
    }
}
