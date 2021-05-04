<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            $table->string('seo_type');
            $table->json('seo_title');
            $table->json('seo_description');
            $table->json('seo_text');
            $table->json('seo_keywords');
            $table->tinyInteger('is_seo_noindex');

            $table->unique(['seo_id', 'seo_type']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('seo');
    }
}
