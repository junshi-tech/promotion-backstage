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

class AdminDept extends Base
{
    /*字段规则*/
    protected $rule = [
        'pid' => 'different:dept_id'
        ,'dept_code' => 'max:10'
        ,'dept_name' => 'require|unique:admin_dept|max:20'
        ,'state' => 'in:0,1'
        ,'sort_num' => 'number'
    ];

    /*返回错误信息*/
    protected $message = [
        'pid.different' => '上级部门不能和当前部门相同！'
        ,'dept_name.require' => '部门名称不能为空！'
        ,'dept_name.unique' => '部门名称已被使用！'
        ,'state.in' => '状态数值不正确！'
        ,'sort_num.number' => '排序的值必须为数字！'
    ];

    protected $scene = [
        'save' => ['pid','dept_code', 'dept_name', 'state', 'sort_num']
    ];
}