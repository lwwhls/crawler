<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Models\Articles;
use App\Models\ArticleContent;


class CrawlerList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler_list {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler some url data';

    protected $articles;
    protected $articleContent;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Articles $articles,ArticleContent $articleContent)
    {
        $this->articles = $articles;
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
        //配置数组
        $config_arr = $this->getConfigSource();

        //获取命令行参数
        $type = $this->argument('type');
        //echo $type;exit;


        if(isset($type)){
            foreach($config_arr as $key=>$value){
                //$type = 'http://news.cctv.com/society/data/index.json';
                if($type == $value['url']){
                    $html = @file_get_contents($type);
                    if(!$html){
                        echo '读取内容出错，抓取地址为:'.$type. "\n";
                    }
                    $obj = $this->getSourceObj($value);

                    $data = $obj->handelList($html);
                   // print_r($data);exit;

                    //插入数据库
                    if($data){
                        $this->insertArticle($data);
                    }else{
                        echo '未抓取到内容'."\n";
                    }
                    break;
                }

            }
        }else{
            foreach($config_arr as $key=>$value){
                $html = @file_get_contents($value['url']);
                if(!$html){
                    echo '读取内容出错，抓取地址为:'.$value['url']. "\n";
                    continue;
                }
                //todo::test
                $obj = $this->getSourceObj($value);

                $data = $obj->handelList($html);
                // print_r($data);exit;

                //插入数据库
                if($data){
                    $this->insertArticle($data);
                }else{
                    echo '未抓取到内容'."\n";
                }
            }

        }
    }


    /*
     * 配置源数组
     *
     * url 对应 爬取数据地址
     * strategy 对应的是策略目录名称.策略文件前缀
     * 如：Cctv.people 对应 Libraries->Cctv->PeopleCrawler.php
     * */
    public function getConfigSource()
    {
        $config_rules = array(
            //央视人物频道
            array(
                'url'=>'http://people.Cctv.com/',
                'strategy'=>'Cctv.people',
                'source' => '央视网',
                'channel' => '人物',
            ),
            //央视军事频道
            array(
                'url'=>'http://military.Cctv.com/data/index.json',
                'strategy'=>'Cctv.military',
                'source' => '央视网',
                'channel' => '军事',
            ),
            //央视军事频道
            array(
                'url'=>'http://news.cctv.com/data/index.json',
                'strategy'=>'Cctv.news',
                'source' => '央视网',
                'channel' => '新闻',
            ),
            //央视社会频道
            array(
                'url'=>'http://news.cctv.com/society/data/index.json',
                'strategy'=>'Cctv.news',
                'source' => '央视网',
                'channel' => '社会',
            ),
            //腾讯科技
            array(
                'url'=>'http://tech.qq.com/',
                'strategy'=>'QQ.tech',
                'source' => '腾讯',
                'channel' => '科技',
            ),
        );
        return $config_rules;

    }

    /*
     *获得数据源处理对象
     * params array $config 数据源配置数组
     * return obj;
     * */
    public function getSourceObj($config)
    {
        $strategy = $config['strategy'];
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
        $obj = new $obj($config);
        return $obj;
    }

    /*
     * 插入数据库
     * */
    public function insertArticle($data)
    {
        if(!is_array($data) || count($data) < 0){
            echo '暂时未读取到数据！'. "\n";
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

            $id = $this->articles->createData($value);
            if($id){
                $content_data = array(
                    'url' => $value['url'],
                    'article_id' => $id,
                    'strategy'=>$value['strategy'],
                    'status'=>0,
                );
                $this->articleContent->createData($content_data);
            }
        }
    }
}
