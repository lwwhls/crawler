<?php
namespace App\Libraries;

use QL\QueryList;
use App\Models\Articles;

class CctvCrawler
{
    public $rulers;
    public $articles;

    public function __construct()
    {
        $this->articles = new Articles();
        $this->defaultRules();
    }


    public function deal_html($contents, $type)
    {
        $ruler = isset($this->rulers[$type]) ? $this->rulers[$type] : [];
        if (!$ruler) {
            echo '该采集类型没有对应的采集规则，请重新选择采集类型！！';
            exit;
        }
        $data = QueryList::Query($contents, $ruler)->data;
        //var_dump($data[0]);//exit;

        $this->insert_data($data);
    }
    /*
     * 处理json
     * */

    public function deal_json($contents, $type)
    {

        $data = json_decode($contents, true);
        if($data && isset($data['rollData'])){
            foreach ($data['rollData'] as $key => $value) {
                $insert_data['title'] = isset($value['title']) ? $value['title'] : '';
                $insert_data['url'] = isset($value['url']) ? $value['url'] : '';
                $insert_data['md5_url'] = isset($value['url']) ? md5($value['url']) : '';
                $insert_data['publish_time'] = isset($value['dateTime']) ? strtotime($value['dateTime']) : '';
                $insert_data['img'] = isset($value['image']) ? $value['image'] : '';
                $insert_data['desc'] = isset($value['description']) ? $value['description'] : '';
                $insert_data['tag'] = isset($value['content']) ? $value['content'] : '';
                $insert_data['source'] = isset($type) ? $type : '';
                //print_r($insert_data);exit;

                $flag = $this->articles->createData($insert_data);
                if($flag){
                    echo "the number {$key} is successful";
                }else{
                    echo "the number {$key} is fail";
                }
            }


        }else{
            echo '暂未读取到数据';
        }

    }
    
    public function insert_data($data)
    {
        if(!is_array($data) || count($data) < 0){
            echo '暂时未读取到数据！';
            exit;
        }
        //处理数据 入库
        foreach ($data as $key => $value) {
            //检查是否重复入库
            $flag = $this->articles->findArticleByUrl(md5($value['url']));
            if ($flag) {
                //已入库 跳过
                continue;
            }

            $insert_data['title'] = isset($value['title']) ? $value['title'] : '';
            $insert_data['url'] = isset($value['url']) ? $value['url'] : '';
            $insert_data['md5_url'] = isset($value['url']) ? md5($value['url']) : '';
            $insert_data['publish_time'] = isset($value['publish_time']) ? strtotime($value['publish_time']) : '';
            $insert_data['img'] = isset($value['img']) ? $value['img'] : '';
            $insert_data['desc'] = isset($value['desc']) ? $value['desc'] : '';
            //print_r($insert_data);exit;

            $flag = $this->articles->createData($insert_data);
            if($flag){
                echo "the number {$key} is successful";
            }else{
                echo "the number {$key} is fail";
            }

        }
    }
    
    public function defaultRules()
    {
        $rulers = array(
            //cctv people频道采集规则
            'news' => [
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
            ],
        );
        $this->rulers = $rulers;
        
    }

}