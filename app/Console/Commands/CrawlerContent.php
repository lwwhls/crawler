<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleContent;

class CrawlerContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler_content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler article content';

    //文章内容表
    protected $articleContent;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ArticleContent $articleContent)
    {
        $this->articleContent = $articleContent;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $content_list = $this->getContentList();
        if($content_list){
            foreach($content_list as $key=>$value){
                //todo:test
                $value['url'] = 'http://tech.qq.com/a/20180420/007644.htm';
                $html = @file_get_contents($value['url']);
                if(!$html){
                    echo '读取内容出错，抓取地址为:'.$value['url'];
                    continue;
                }
                $obj = $this->getSourceObj($value['strategy']);

                $data = $obj->handelContent($html);
                print_r($data);exit;

                //插入数据库
                $this->insertArticle($data);
            }

        }else{
            echo "没有需要抓取的数据"."\n";
        }
    }


    /*
     *获得数据源处理对象
     * params string $strategy  格式Cctv.people;
     * return obj;
     * */
    public function getSourceObj($strategy)
    {
        if(!$strategy){
            echo "参数不合法"."\n";
            exit;
        }
        $arr = explode('.',$strategy);
        $dir_name = ucfirst($arr[0]);
        $obj_name = ucfirst($arr[1]).'Crawler';

        $path = app_path()."/Libraries/".$dir_name;
        if(!is_dir($path)){
            echo "文件路径不存在"."\n";
            exit;
        }

        $obj = '\App\Libraries\\'.$dir_name.'\\'.$obj_name;
        $obj = new $obj();
        return $obj;
    }

    /*
     * 插入数据库
     * */
    public function insertArticle($data)
    {
        if(!is_array($data) || count($data) < 0){
            echo '暂时未读取到数据！';
            exit;
        }
        //处理数据 入库
        foreach ($data as $key => $value) {
            //检查是否重复入库
            $flag = $this->articles->findArticleByUrl($value['md5_url']);
            if ($flag) {
                //已入库 跳过
                continue;
            }
            echo "number {$key} is inserting"."\n";

            $this->articles->createData($value);exit;
        }
    }
    /*
     *获取未抓取内容的url列表
     *
     * */
    public function getContentList()
    {
        $conditions = array('status'=>0);
        $data = $this->articleContent->getAll($conditions);
        return $data;
    }

}
