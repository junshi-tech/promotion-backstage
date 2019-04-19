<?php


namespace app\soldier\validate;

use app\common\validate\Base;

class PicSoldier extends Base
{
    /*字段规则*/
    protected $rule = [
        'user_id' => 'max:36'
        ,'username' => 'require|max:10'
        ,'phone' => 'require|mobile'
        ,'join_time' => 'date'
        ,'rank' => 'integer|<=:3'
        ,'type' => 'integer|<=:5'
        ,'img_url' => 'max:1000'
    ];

    /*返回错误信息*/
    protected $message = [
        'user_id.max' => '用户id不能超过36个字符'
        ,'username.require' => '姓名不能为空'
        ,'username.max' => '姓名不能超过10个字'
        ,'phone.require' => '手机号不能为空'
        ,'phone.mobile' => '手机号码格式错误'
        ,'join_time.date' => '入伍日期格式错误'
        ,'rank' => '职级数据错误'
        ,'type' => '兵种数据错误'
        ,'img_url' => '图片集路径不能超过1000个字符'
    ];

    /*字段规则*/
    protected $scene = [
        'save' => ['user_id','username','phone','join_time','rank','type','img_url']
    ];

}