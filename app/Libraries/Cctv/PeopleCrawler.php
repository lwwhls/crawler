<?php
namespace App\Libraries\Cctv;

use QL\QueryList;
use App\Libraries\CrawlerInterface;

class PeopleCrawler implements CrawlerInterface
{
    public $rulers;
    public $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->rulers = $this->listRules();
    }

    /*
     * 爬取并且处理数据
     * */
    public function handelList($html)
    {
        $data = QueryList::Query($html, $this->rulers)->data;

        $insert_data = [];
        if ($data) {
            foreach ($data as $key => $value) {
                $insert_data[$key]['title'] = isset($value['title']) ? $value['title'] : '';
                $insert_data[$key]['url'] = isset($value['url']) ? $value['url'] : '';
                $insert_data[$key]['md5_url'] = isset($value['url']) ? md5($value['url']) : '';
                $insert_data[$key]['publish_time'] = isset($value['publish_time']) ? $value['publish_time'] : '';
                $insert_data[$key]['img'] = isset($value['img']) ? $value['img'] : '';
                $insert_data[$key]['desc'] = isset($value['desc']) ? $value['desc'] : '';
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
            'content'=>array('.cnt_bd','html','-h1 -h2 -.function -#embed_playerid -script'),
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
        return  array(
            //采集文章标题
            'title' => ['.right h2 a', 'text'],
            //采集链接
            'url' => ['.right h2 a', 'href'],
            //采集文章发布日期
            'publish_time' => ['.key>i', 'text',],
            //采集图片
            'img' => ['.left img', 'src'],
            //采集摘要
            'desc' => ['.right p', 'text'],
        );

    }
}