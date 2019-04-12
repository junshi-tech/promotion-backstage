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

class AdminRole extends Base
{
    /*字段规则*/
    protected $rule = [
        'role_name' => 'require|unique:user_role|max:15'
        , 'auth' => 'require|max:1000'
        , 'describe' => 'max:1000'
        , 'sort_num' => 'number'
        , 'state' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        "role_name.require" => '角色名称不能为空！'
        , "role_name.unique" => '角色名称已被使用，不能重复！'
        , "role_name.max" => '角色名称不能超过15字！'
        , "auth.require" => '至少选择一项权限！'
        , "describe.max" => '描述不能超过1000字！'
        , 'sort_num.number' => '排序的值必须为数字！'
        , 'state.in' => '状态数值不正确！'
    ];

    protected $scene = [
        'save' => ['role_name', 'auth', 'describe', 'sort_num', 'state']

    ];

}