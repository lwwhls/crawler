<?php
namespace App\Libraries;

use QL\QueryList;

class CctvCrawler
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
        print_r($data);exit;
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
            'news' =>[
                //采集文章标题
                'title' => ['h2','text'],
                //采集链接
                'link' => ['.left>a','href'],
                //采集文章发布日期
                'date' => ['.key>i','text',],
                //采集图片
                'img' => ['.left>img','src'],
            ],
        );
        $this->rulers = $rulers;
        
    }

}