<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\validate;

use app\common\validate\Base;

class BasicInfo extends Base
{
    /*字段规则*/
    protected $rule = [
        'pid' => 'require|different:basic_id'
        ,'basic_name' => 'require|max:50'
        ,'image' => 'max:255'
        ,'basic_value' => 'max:255'
        ,'sort_num' => 'number'
        ,'state' => 'in:0,1'
        ,'describe' => 'max:1000'
        ,'is_system' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        'pid.different' => '上级资料不能和当前资料相同！'
        ,"basic_name.require" => '资料名称不能为空！'
        ,"basic_name.max" => '资料名称不能超过50字！'
        ,"image.max" => '图片地址不能超过255个字母！'
        ,"basic_value.max" => '资料值不能超过255字！'
        ,"sort_num.number" => '排序的值必须为数据！'
        ,"state.in" => '“状态”的值不正确！'
        ,"describe.max" => '简介不能超过1000字！'
        ,"is_system.in" => '“是否系统默认值”的值不正确！'
    ];

    protected $scene = [
        'save' => ['pid', 'basic_name', 'image', 'basic_value', 'sort_num', 'state', 'describe', 'is_system']
    ];
}