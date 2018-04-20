<?php
namespace App\Libraries\Cctv;

use QL\QueryList;
use App\Libraries\CrawlerInterface;

class MilitaryCrawler implements CrawlerInterface
{
    public $rulers;
    public $config =array(
        'source' => '央视网',
        'channel' => '军事',
        'strategy'=>'Cctv.military'
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
        $conten_rule =array();
        $data = QueryList::Query($html, $conten_rule,'.Cnt-Main-Article-QQ')->data;
        return $data;
    }
    /*
     * 设置默认规则
     * */
    public function listRules()
    {
        return  array();
    }

}