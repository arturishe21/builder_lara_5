<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTranslationsCmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translations_cms', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->increments('id');
            $table->integer('translations_phrases_cms_id')->unsigned();
            $table->string('lang', 2);
            $table->text('translate');

            $table->index('translations_phrases_cms_id');

            $table->foreign('translations_phrases_cms_id')
                ->references('id')->on('translations_phrases_cms')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations_cms');
    }
}
