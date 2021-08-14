<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_contents', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id');
            $table->string('dirname');
            $table->string('displayname');
            $table->string('savedname');
            $table->string('link');
            $table->string('extension');
            $table->bigInteger('bytes');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_contents');
    }
}
