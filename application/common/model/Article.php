<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Db;

class Article extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'art_id';

    /**
     * 获取文章推荐等级，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getLevelTextAttr($value, $data)
    {
        $value = $data['level'] ?? $value;
        $item = ['0'=>'未审核','1'=>'正常','2'=>'推荐','3'=>'头条'];
        return $item[$value] ?? '';
    }

    /**
     * 获取栏目，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getCatNameAttr($value, $data)
    {
        return Db::name('article_cat')->where('cat_id', $data['cat_id'])->value('cat_name');
    }

    /**
     * 格式化发布时间
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getPublicDateAttr($value, $data)
    {
        return !empty($data['public_time']) ? date('Y-m-d', $data['public_time']) : '';
    }

    /**
     * 格式化发布时间
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getPublicDateHhIiSsAttr($value, $data)
    {
        return !empty($data['public_time']) ? date('Y-m-d H:i:s', $data['public_time']) : '';
    }

    /**
     * 保存“发布时间”
     * @param $value
     * @return mixed
     */
    public function setPublicTimeAttr($value)
    {
        return !empty($value) ? strtotime($value) : strtotime(date('Y-m-d H:i:s'));
    }

    /**
     * 保存“文章详情”，html转义
     * @param $value
     * @return mixed
     */
    public function setContentAttr($value)
    {
        return !empty($value) ? htmlspecialchars($value) : '';
    }

    /**
     * 获取“文章详情”，html转义
     * @param $value
     * @return mixed
     */
    public function getContentAttr($value)
    {
        return !empty($value) ? htmlspecialchars_decode($value) : '';
    }




}