<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disk', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->Text('description');
            $table->string('other');
            $table->integer('types')->default(1);
            $table->string('client_id');
            $table->string('client_secret');
            $table->Text('token');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disk');
    }
}
