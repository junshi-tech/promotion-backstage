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

class BasicBanner extends Base
{
    /*字段规则*/
    protected $rule = [
        'location' => 'require'
        , 'img_url' => 'max:255'
        , 'img_name' => 'require|max:100'
        , 'img_name_style' => 'max:255'
        , 'describe' => 'max:1000'
        , 'link' => 'max:255'
        , 'language' => 'in:0,1'
        , 'state' => 'in:0,1'
        , 'sort_num' => 'number'
    ];

    /*返回错误信息*/
    protected $message = [
        'location.require' => '请选择Banner位置！'
        , 'img_url.max' => '图片地址不能超过255个字母！'
        , 'img_name.require' => '图片名称不能为空！'
        , 'img_name.max' => '图片名称不能超过100字！'
        , 'img_name_style.max' => '名称样式不能超过100个字母！'
        , 'describe.max' => '描述不能超过100字！'
        , 'link.max' => '跳转链接不能超过255个字母！'
        , 'language.in' => '语言版本数值不正确！'
        , 'state.in' => '状态数值不正确！'
        , 'sort_num.number' => '排序的值必须为数字！'
    ];

    protected $scene = [
        'save' => ['location', 'img_url', 'img_name', 'img_name_style', 'describe', 'link', 'language', 'state', 'sort_num']
    ];
}