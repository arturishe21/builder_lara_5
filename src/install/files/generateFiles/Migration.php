<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatetableUpName extends Migration
{
    public function up(): void
    {
        Schema::create('tableName', function (Blueprint $table) {
            $table->collation = 'utf8_general_ci';
            $table->charset = 'utf8';

            $table->id();
            $table->fieldsReplace;
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tableName');
    }
}
