<?php
namespace App\Libraries;

use QL\QueryList;

class SinaCrawler
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
            //sina ent频道采集规则
            'ent' =>[
                //采集文章标题
                'title' => ['h3.ty-card-tt>a','text'],
                //采集链接
                'link' => ['h3.ty-card-tt>a','href'],
                //采集文章发布日期
                'date' => ['.ty-card-time','text',],
                //采集图片
                'img' => ['ty-card-thumbs-w .ty-card-thumbs-c img','src'],
            ],
        );
        $this->rulers = $rulers;
        
    }

}