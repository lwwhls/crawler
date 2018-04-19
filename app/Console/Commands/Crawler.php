<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Libraries;
use  DB;

class Crawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler some url data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //爬虫对象
        $cctv = new Libraries\CctvCrawler();
        $qq = new Libraries\QqCrawler();

        //配置数组
        $config_arr = $this->configSource();

        $type = $this->argument('type');
        if(isset($config_arr[$type])){
            $html = @file_get_contents($type);
            if(!$html){
                echo '读取内容出错，地址为:'.$type;
            }
            $value = $config_arr[$type];

            $obj = ${$value['file']};
            $obj->$value['method']($html,$value['type']);

        }else{
            foreach($config_arr as $key=>$value){
                $html = @file_get_contents($key);
                if(!$html){
                    echo '读取内容出错，地址为:'.$key;
                    continue;
                }
                //$this->getSql();
                $obj = ${$value['file']};
                $obj->$value['method']($html,$value['type']);
            }
        }

    }

    public function configSource()
    {
        $config_rules = array(
            //央视人物频道
            'http://people.cctv.com/'=>array(
                'file'=>'cctv',
                'method'=>'deal_html',
                'type'=>'news',
            ),
            //央视军事频道
            'http://military.cctv.com/data/index.json'=>array(
                 'file'=>'cctv',
                 'method'=>'deal_json',
                 'type'=>'military',
             ),
             //qq 理财频道
            /* 'http://money.qq.com/'=>array(
                 'file'=>'qq',
                 'method'=>'deal_html',
                 'type'=>'money',
             )*/
        );
        return $config_rules;

    }
    public function getSql ()
    {
        //只使用5.2版本以下laravel框架
        \DB::listen(function ($sql, $bindings, $time) {
            foreach ($bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $sql = preg_replace('/\?/', $value, $sql, 1);
            }
            $sql .= ';  耗时：' . $time;
            dump($sql);
        });
    }
}
