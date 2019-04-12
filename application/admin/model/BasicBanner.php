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
use think\facade\Request;

class BasicBanner extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'banner_id';

    /**
     * 获取完整图片路径
     * @param $value
     * @param $data
     * @return string
     */
    public function getImgUrlFullAttr($value, $data)
    {
        $value = $data['img_url'] ?? $value;
        return !empty($value) ? Request::domain().$value : '';
    }

    /**
     * 获取位置，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getLocationTextAttr($value, $data)
    {
        $value = isset($data['location']) ? $data['location'] : $value;
        return Db::name('basic_info')->where('basic_id', $value)->value('basic_name');
    }

    /**
     * 获取状态，对应的中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStateTextAttr($value, $data)
    {
        $item = ['0'=>'否', '1'=>'是'];
        $value = $data['state'] ?? $value;
        return  $item[$value] ?? '';
    }
}