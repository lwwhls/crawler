<?php
namespace App\Libraries\QQ;

use QL\QueryList;
use App\Libraries\CrawlerInterface;

class TechCrawler implements CrawlerInterface
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

        $insert_data = [];
        $data = QueryList::query($html, $this->rulers, '.list .Q-tpList', 'UTF-8', 'GB2312')->getData(function ($item) {
            if (isset($item['tag'])) {
                //$item['tag'] = QueryList::query($item['tag'], array('pp' => array('.columnlist','text')))->data;
            }
            return $item;
        });

        if ($data) {
            foreach ($data as $key => $value) {
                $insert_data[$key]['title'] = isset($value['title']) ? $value['title'] : '';
                $insert_data[$key]['url'] = isset($value['url']) ? $value['url'] : '';
                $insert_data[$key]['md5_url'] = isset($value['url']) ? md5($value['url']) : '';
                $insert_data[$key]['publish_time'] = isset($value['publish_time']) ? $value['publish_time'] : '';
                $insert_data[$key]['img'] = isset($value['img']) ? $value['img'] : '';
                $insert_data[$key]['tag'] = isset($value['tag']) ? $value['tag'] : '';
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
        $conten_rule = array(
            'content' => array('#Cnt-Main-Article-QQ','html')
        );
        $data = QueryList::Query($html, $conten_rule,'','UTF-8', 'GB2312')->getData(function($item){
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
        return array(
            //采集文章标题
            'title' => ['.itemtxt h3 a', 'text'],

            //采集链接
            'url' => ['.itemtxt h3 a', 'href'],

            //采集文章标签
            'tag' => ['.timelabel .techTag>em', 'text'],

            //发布时间
            'publish_time' => ['.timelabel .aTime', 'text'],

            //采集图片
            'img' => ['.pic img', 'src'],

        );

    }

}