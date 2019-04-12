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

class Article extends Base
{
    /*字段规则*/
    protected $rule = [
        'title' => 'require|max:100'
        ,'title_sub' => 'max:255'
        ,'keywords' => 'max:255'
        ,'describe' => 'max:1000'
        ,'link' => 'max:255'
        ,'img_url' => 'max:255'
        ,'sort_num' => 'number'
        ,'people_num' => 'number'
        ,'read_num' => 'number'
        ,'praise_num' => 'number'
        ,'state' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        'title.require' => '标题不能为空！'
        ,'title.max' => '标题不能超过100字！'
        ,'title_sub.max' => '副标题不能超过100字！'
        ,'keywords.max' => '关键词不能超过255字！'
        ,'describe.max' => '描述不能超过1000字！'
        ,'link.max' => '外链不能超过255字符！'
        ,'img_url.max' => '图片链接不能超过255字符！'
        ,'sort_num.number' => '排序必须为纯数字！'
        ,'people_num.number' => '人数必须为纯数字！'
        ,'read_num.number' => '阅读数必须为纯数字！'
        ,'praise_num.number' => '点赞数必须为纯数字！'
        ,'state.in' => '状态值不正确！'
    ];

    protected $scene = [
        'save' => ['title','title_sub','keywords','describe','link','img_url','sort_num','people_num','read_num',
            'praise_num','state']
    ];
}