<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:12
// +----------------------------------------------------------------------

namespace app\admin\validate;

use app\common\validate\Base;

class BasicMenu extends Base
{
    /*字段规则*/
    protected $rule = [
        'pid' => 'different:menu_id'
        , 'menu_name' => 'require|max:100'
        , 'url' => 'max:255'
        , 'params' => 'max:255'
        , 'is_extend' => 'in:0,1'
        , 'open_type' => 'in:0,1'
        , 'display' => 'in:0,1'
        , 'only_admin' => 'in:0,1'
        , 'sort_num' => 'number'
        , 'describe' => 'max:1000'
        , 'icon' => 'max:100'
    ];

    /*返回错误信息*/
    protected $message = [
        'pid.different' => '上级菜单不能和当前菜单相同！'
        , 'menu_name.require' => '菜单名称不能为空！'
        , 'menu_name.max' => '菜单名称不能超过100字！'
        , 'url.max' => '链接不能超过255个字母！'
        , 'params.max' => '参数不能超过255个字母！'
        , 'is_extend.in' => '“是否默认展开”数值不正确！'
        , 'open_type.in' => '“打开方式”数值不正确！'
        , 'display.in' => '“显示状态”数值不正确！'
        , 'only_admin.in' => '“显示状态”数值不正确！'
        , 'sort_num.number' => '排序的值必须为数字！'
        , 'describe.max' => '描述不能超过1000字！'
        , 'icon.max' => '样式类名不能超过100个字母！'
    ];

    protected $scene = [
        'save' => ['pid', 'menu_name', 'url', 'params', 'is_extend', 'open_type', 'display', 'only_admin', 'sort_num', 'describe', 'icon']
    ];
}

