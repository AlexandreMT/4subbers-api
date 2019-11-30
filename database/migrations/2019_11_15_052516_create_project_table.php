<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    public function up()
    {
        Schema::create('project', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('url');
            $table->string('originalSubtitle');
            $table->timestamps();
        });

        Schema::create('part', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->foreign('id')->references('id')->on('project');
            $table->string('fileName');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('project', function (Blueprint $table) {
            //
        });
    }
}
