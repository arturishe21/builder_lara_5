<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('translations_phrases')) {
            Schema::create('translations_phrases', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->text('phrase')->collation('utf8_bin');
            });
        }

        if (! Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
                $table->engine = 'InnoDB';

                $table->increments('id');
                $table->integer('id_translations_phrase')->unsigned();
                $table->string('lang', 2);
                $table->text('translate');

                $table->index('id_translations_phrase');
                $table->foreign('id_translations_phrase')->references('id')->on('translations_phrases')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('translations');
        Schema::drop('translations_phrases');
    }
}
