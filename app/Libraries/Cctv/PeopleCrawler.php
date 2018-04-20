<?php
namespace App\Libraries\Cctv;

use QL\QueryList;
use App\Libraries\CrawlerInterface;

class PeopleCrawler implements CrawlerInterface
{
    public $rulers;
    public $config =array(
        'source' => '央视网',
        'channel' => '人物',
        'strategy'=>'Cctv.people'
    );

    public function __construct()
    {
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
        $conten_rule =array();
        $data = QueryList::Query($html, $conten_rule,'.Cnt-Main-Article-QQ')->data;
        return $data;
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