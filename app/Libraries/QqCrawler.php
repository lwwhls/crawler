<?php
namespace App\Libraries;

use QL\QueryList;
use App\Models\Articles;

class QqCrawler
{
    public  $rulers;
    public function __construct()
    {
        $this->defaultRules();
    }

    public function deal_html($contents,$type)
    {
        $ruler  = isset($this->rulers[$type]) ? $this->rulers[$type] : [];
        if(!$ruler){
            echo '该采集类型没有对应的采集规则，请重新选择采集类型！！';exit;
        }
        $data = QueryList::Query($contents,$ruler)->data;
        print_r($data);
    }

    public function deal_json($contents)
    {

        $data = json_decode($contents, true);
        var_dump($data);

    }
    
    public function defaultRules()
    {
        $rulers = array(
            //cctv people频道采集规则
            'money' =>[
                //采集文章标题
                'title' => ['.linkto','text'],
                //采集链接
                'link' => ['.linkto','href'],

            ],
        );
        $this->rulers = $rulers;
        
    }

}