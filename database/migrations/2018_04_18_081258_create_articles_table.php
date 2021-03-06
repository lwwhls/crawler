<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            // 自增id
            $table->increments('id')->unsigned();

            // 标题
            $table->string('title', 100)->defalut('');

            // 作者
            $table->string('author', 100)->defalut('');

            // 文章摘要
            $table->string('desc',500)->default('');

            // 文章url
            $table->string('url',200)->default('');

            //文章url md5 加密
            $table->string('md5_url',32)->default('');

            //文章标签
            $table->string('tag',200)->default('');

            //文章摘要图片
            $table->string('img',500)->default('');

            //文章来源
            $table->string('source',200)->default('');

            //文章来源栏目
            $table->string('channel',200)->default('');

            //文章抓取策略 strategy
            $table->string('strategy',100)->default('');

            //文章发布时间
            $table->timestamp('publish_time')->nullable();

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
        //
    }
}
