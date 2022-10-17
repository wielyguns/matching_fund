<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cms_managements', function (Blueprint $table) {
            $table->enum('type', ['IMAGE', 'STRING', 'URL']);
            $table->string('example');
            $table->boolean('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cms_managements', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
