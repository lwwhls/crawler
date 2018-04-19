<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Articles extends Model
{
    //与模型关联的数据表
    protected $table = 'articles';

    //定义数据表的主键
    public $primaryKey = 'id';

    //模型中是否出现created_at 和 updated_at 时间字段
    public $timestamps = true;

    //可以/不允许 批量赋值的字段
    protected $guarded = [];

    /**
     * 数据列表
     *
     * @param array $data
     * @return mixed
     */
    public function allData($columns = array('*'))
    {
        return $this->get($columns)->toArray();
    }

    /*
     * 单条创建创建文章
     */
    public function createData($data)
    {
        $result = $this->create($data);
        return $result->id ? $result->id : 0;
    }
    /*
     * 判断文章是否存在
     *
     *  */
    public function findArticleByUrl($md5_url)
    {
        $result = $this->where('md5_url',$md5_url)->get()->toArray();
        return empty($result) ? false : true;
    }





}
