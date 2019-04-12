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

class Goods extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'goods_id';

    /**
     * 获取推荐等级，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getLevelTextAttr($value, $data)
    {
        return Db::name('basic_info')->where('basic_id', $data['level'])->value('basic_name');
    }

    /**
     * 获取年份
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getYearsTextAttr($value, $data)
    {
        return Db::name('goods_cat')->where('cat_id', $data['years'])->value('cat_name');
    }

    /**
     * 获取类目，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getCatNameAttr($value, $data)
    {
        return Db::name('goods_cat')->where('cat_id', $data['cat_id'])->value('cat_name');
    }

    /**
     * 获取国家，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getCountryTextAttr($value, $data)
    {
        return Db::name('goods_cat')->where('cat_id', $data['country'])->value('cat_name');
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
     * 保存“产品名称样式”，html转义
     * @param $value
     * @return mixed
     */
    public function setGoodsNameStyleAttr($value)
    {
        return !empty($value) ? htmlspecialchars($value) : '';
    }

    /**
     * 获取“产品名称样式”，html转义
     * @param $value
     * @return mixed
     */
    public function getGoodsNameStyleAttr($value)
    {
        return !empty($value) ? htmlspecialchars_decode($value) : '';
    }

    /**
     * 保存“产品详情”，html转义
     * @param $value
     * @return mixed
     */
    public function setDetailAttr($value)
    {
        return !empty($value) ? htmlspecialchars($value) : '';
    }

    /**
     * 获取“产品详情”，html转义
     * @param $value
     * @return mixed
     */
    public function getDetailAttr($value)
    {
        return !empty($value) ? htmlspecialchars_decode($value) : '';
    }

    /**
     * 保存“产品类目”
     * @param $value
     * @return mixed
     */
    public function setCatIdAttr($value)
    {
        if (!empty($value)) {
            $arr = explode('/', $value);
        }
        return !empty($arr) ? end($arr) : 0;
    }


}