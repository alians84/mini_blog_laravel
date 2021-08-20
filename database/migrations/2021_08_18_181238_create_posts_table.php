<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->bigIncrements('post_id');
            $table->bigInteger('author_id')->unsigned()->nullable();
            $table->string('title',100);
            $table->string('excerpt',200);
            $table->string('image',100)->nullable();
            $table->text('body');
            $table->timestamps( );
            //внешний кдюч,ссыдается на поле id таблицы users
            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
