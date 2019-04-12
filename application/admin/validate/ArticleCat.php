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

class ArticleCat extends Base
{
    /*字段规则*/
    protected $rule = [
        'cat_type' => 'require'
        ,'cat_name' => 'require|max:30'
        ,'keywords' => 'max:255'
        ,'describe' => 'max:1000'
        ,'sort_num' => 'number'
        ,'state' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        'cat_type.require' => '请选择“板块”！'
        ,'cat_name.require' => '名称不能为空！'
        ,'cat_name.max' => '名称不能超过30字！'
        ,'keywords.max' => '关键词不能超过255字！'
        ,'describe.max' => '描述不能超过1000字！'
        ,'sort_num.number' => '排序必须为纯数字！'
        ,'state.in' => '状态值不正确！'
    ];

    protected $scene = [
        'save' => ['cat_type','cat_name','keywords','describe','sort_num','state']
    ];
}