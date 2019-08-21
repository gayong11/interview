<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTopicOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topic_options', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('topic_id')->comment('题目ID');
            $table->foreign('topic_id')->references('id')->on('topics')->onDelete('cascade');
            $table->string('option')->comment('选择值');
            $table->string('content')->comment('选项内容');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('topic_options');
    }
}
