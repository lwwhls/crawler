<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ArticleContent;

class CrawlerContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawler_content';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'crawler article content';

    //文章内容表
    protected $articleContent;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ArticleContent $articleContent)
    {
        $this->articleContent = $articleContent;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $content_list = $this->getContentList();
        if ($content_list) {
            foreach ($content_list as $key => $value) {
                /* if($value['id'] == 448){
                      $html = @file_get_contents($value['url']);
                      if (!$html) {
                          echo '读取内容出错，抓取地址为:' . $value['url'];
                          continue;
                      }
                      $obj = $this->getSourceObj($value['strategy']);
                      $data = $obj->handelContent($html);
                      //var_dump($data);exit;
                      if ($data) {
                          $update_data = array(
                              'contents' => $data,
                              'status' => 1,
                          );
                          $this->updateArticleContent($update_data, $value['id']);
                      } else {
                          echo '暂未抓取到数据';
                      }
                      exit;
                  }*/
                $html = @file_get_contents($value['url']);
                if (!$html) {
                    echo '读取内容出错，抓取地址为:' . $value['url'] . "\n";
                    continue;
                }
                $obj = $this->getSourceObj($value['strategy']);
                $data = $obj->handelContent($html);
                //var_dump($data);exit;
                if ($data) {
                    $update_data = array(
                        'contents' => $data,
                        'status' => 1,
                        'crawler_times' => $value['crawler_times'] + 1
                    );
                    $this->updateArticleContent($update_data, $value['id']);
                } else {
                    $update_data = array(
                        'crawler_times' => $value['crawler_times'] + 1
                    );
                    $this->updateArticleContent($update_data, $value['id']);
                    echo 'no data' . "\n";
                }

            }

        } else {
            echo "no data" . "\n";
        }
    }


    /*
     *获得数据源处理对象
     * params string $strategy  格式Cctv.people;
     * return obj;
     * */
    public function getSourceObj($strategy)
    {
        if (!$strategy) {
            echo "参数不合法" . "\n";
            exit;
        }
        $arr = explode('.', $strategy);
        $dir_name = ucfirst($arr[0]);
        $obj_name = ucfirst($arr[1]) . 'Crawler';

        $path = app_path() . "/Libraries/" . $dir_name;
        if (!is_dir($path)) {
            echo "文件路径不存在" . "\n";
            exit;
        }

        $obj = '\App\Libraries\\' . $dir_name . '\\' . $obj_name;
        $obj = new $obj($arr = ['strategy' => $strategy]);
        return $obj;
    }

    /*
     *更新文章正文内容
     *
     * */
    public function updateArticleContent($data, $id)
    {
        //查找数据
        $content = $this->articleContent->where('id', $id)->first()->toArray();
        if ($content && $content['status'] == 0) {
            echo 'id:' . $id . " update content success" . "\n";
            $this->articleContent->updateData($data, $id);
        } else {
            echo '参数有问题，对应id为:' . $id . "\n";;
        }

    }

    /*
     *获取未抓取内容的列表
     *
     * */
    public function getContentList()
    {
        $where = array('status' => 0);
        $data = $this->articleContent->getAll($where);
        return $data;
    }

}
