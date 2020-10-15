<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIndexUsersRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('persistences', function (Blueprint $table) {
            $table->index(['code']);
        });

        Schema::table('throttle', function (Blueprint $table) {
            $table->index(['type', 'ip', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('persistences', function (Blueprint $table) {
            $table->dropIndex(['code']);
        });

        Schema::table('throttle', function (Blueprint $table) {
            $table->dropIndex(['type', 'ip', 'created_at']);
        });
    }
}
