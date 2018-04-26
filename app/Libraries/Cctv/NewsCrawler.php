<?php
namespace App\Libraries\Cctv;

use QL\QueryList;
use App\Libraries\CrawlerInterface;

class NewsCrawler implements CrawlerInterface
{
    public $rulers;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->rulers = $this->listRules();
    }

    /*
     * 爬取列表处理数据
     * */
    public function handelList($html)
    {
        $insert_data = [];
        $data = json_decode($html, true);
        if ($data && isset($data['rollData'])) {
            foreach ($data['rollData'] as $key => $value) {
                $insert_data[$key]['title'] = isset($value['title']) ? $value['title'] : '';
                $insert_data[$key]['url'] = isset($value['url']) ? $value['url'] : '';
                $insert_data[$key]['md5_url'] = isset($value['url']) ? md5($value['url']) : '';
                $insert_data[$key]['publish_time'] = isset($value['dateTime']) ? $value['dateTime'] : '';
                $insert_data[$key]['img'] = isset($value['image']) ? $value['image'] : '';
                $insert_data[$key]['desc'] = isset($value['description']) ? $value['description'] : '';
                $insert_data[$key]['source'] = $this->config['source'];
                $insert_data[$key]['channel'] = $this->config['channel'];
                $insert_data[$key]['strategy'] = $this->config['strategy'];
            }
        }
        return $insert_data;
    }
    /*
     * 爬取正文数据
     * */
    public function handelContent($html)
    {
        $content_rule =array(
            'content'=>array('.cnt_bd','html','-h1 -h2 -.function -.o-tit -script'),
        );
        $data = QueryList::Query($html, $content_rule)->getData(function($item){
            return $item['content'];
        });
        $return_data = '';
        if($data){
            $return_data = implode(' ',array_values($data));
        }
        return $return_data;
    }
    /*
     * 设置默认规则
     * */
    public function listRules()
    {
        return  array();
    }

}