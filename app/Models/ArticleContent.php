<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleContent extends Model
{
    //与模型关联的数据表
    protected $table = 'article_contents';

    //定义数据表的主键
    public $primaryKey = 'id';

    //模型中是否出现created_at 和 updated_at 时间字段
    public $timestamps = true;

    //可以/不允许 批量赋值的字段
    protected $guarded = [];

    /*
     * 单条创建创建文章内容
     */
    public function createData($data)
    {
        $result = $this->create($data);
        return $result->id ? $result->id : 0;
    }

    /*
    * 修改创建文章内容
    */
    public function updateData($data,$id)
    {
        return $this->where('id',$id)->update($data);
    }

    /*
     * 获取列表数据
     * */
    public function getAll($conditions)
    {
        $result = $this->where($conditions)->get()->toArray();
        return $result;
    }
}
