<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class ArticleCat extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'cat_id';

    /**
     * 获取数据列表TableTree
     * @param array $map
     * @return string
     */
    public function getDataTableTree($map = [])
    {
        $list = $this->where($map)
            ->field('cat_id,pid,cat_name,cat_type,location,create_time')
            ->append(['cat_type_text', 'location_text'])
            ->order('sort_num asc, cat_id desc')
            ->select()
            ->toArray();
        return \Tree::get_Table_tree($list, 'cat_name', 'cat_id');
    }

    /**
     * 获取显示位置
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getLocationTextAttr($value, $data)
    {
        $value = $data['location'] ?? $value;
        return !empty($value) ? Db::name('basic_info')->where('basic_id', $value)->cache(5)->value('basic_name') : '';
    }

    /**
     * 获取所属板块
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getCatTypeTextAttr($value, $data)
    {
        $value = $data['cat_type'] ?? $value;
        return !empty($value) ? Db::name('basic_info')->where('basic_id', $value)->cache(5)->value('basic_name') : '';
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array
     */
    public function handleSaveData($data = [])
    {
        if (empty($data['sort_num'])) {
            $sort = Db::name('article_cat')->where('cat_type', $data['cat_type'])->where('pid', $data['pid'])->max('sort_num');
            $data['sort_num'] = $sort + 1;
        }
        return $data;
    }
}