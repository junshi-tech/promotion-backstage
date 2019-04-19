<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Config;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class User extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'user_id';

    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 获取出生日期
     * @param $value
     * @return false|string
     */
    public function getBirthdayAttr($value)
    {
        return $value == '0000-00-00' ? '' : $value;
    }

    /**
     * 保存出生日期
     * @param $value
     * @return false|string
     */
    public function setBirthdayAttr($value)
    {
        return $this->saveToYyyyMmDd($value);
    }

    /**
     * 保存为 年-月-日
     * @param $data
     * @return false|string
     */
    public function saveToYyyyMmDd($data)
    {
        if (empty($data)) {
            $res = '1971-01-01';
        } elseif (is_int($data)) {
            $res = date('Y-m-d', $data);
        } else {
            $res = $data;
        }
        return $res;
    }

    /**
     * 获取性别中文称呼
     * @param $value
     * @return string
     */
    public function getSexTextAttr($value, $data)
    {
        return $data['sex'] ? '男' : '女';
    }

    /**
     * 获取登录状态“state” 中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStateTextAttr($value, $data)
    {
        $item = ['-1'=>'待入职', '0'=>'冻结', '1'=>'正常', '2'=>'调试'];
        return $item[$data['state']];
    }

}