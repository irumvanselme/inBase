<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListsTitlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lists_titles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("lists_id");
            $table->string("title");
            $table->string("type");

            $table->index("lists_id");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lists_titles');
    }
}
