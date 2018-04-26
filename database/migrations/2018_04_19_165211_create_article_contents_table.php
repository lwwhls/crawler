<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_contents', function (Blueprint $table) {
            // 自增id
            $table->increments('id')->unsigned();

            //文章id
            $table->integer('article_id');

            // 文章url
            $table->string('url',200)->default('');

            //文章内容
            $table->text('contents')->default('');

            //文章抓取策略 strategy
            $table->string('strategy',100)->default('');

            //文章状态 {0 未抓取 1 已抓取}
            $table->tinyInteger('status')->default(0);

            //文章抓取次数
            $table->tinyInteger('crawler_times')->default(0);

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
        Schema::drop('article_contents');
    }
}
