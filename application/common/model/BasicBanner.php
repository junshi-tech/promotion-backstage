<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\model;

class BasicBanner extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'banner_id';

    /**
     * 保存“名称样式”，html转义
     * @param $value
     * @return mixed
     */
    public function setImgNameStyleAttr($value)
    {
        return !empty($value) ? htmlspecialchars($value) : '';
    }

    /**
     * 获取“名称样式”，html转义
     * @param $value
     * @return mixed
     */
    public function getNameStyleAttr($value)
    {
        return !empty($value) ? htmlspecialchars_decode($value) : '';
    }


}