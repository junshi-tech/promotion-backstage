<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\validate;

use app\common\validate\Base;

class User extends Base
{
    /*字段规则*/
    protected $rule = [
         'user_name' => 'require|max:10'
        , 'user_pwd' => 'max:32'
        , 'tel' => 'require|unique:user|checkPwd|max:11'
        , 'card_type' => 'number'
        , 'card_no' => 'max:32'
        , 'sex' => 'in:0,1'
        , 'province' => 'number'
        , 'city' => 'number'
        , 'district' => 'number'
        , 'address' => 'max:100'
        , 'bank' => 'number'
        , 'bank_no' => 'max:25'
        , 'email' => 'max:20'
        , 'describe' => 'max:1000'
        , 'openid' => 'max:28'
        , 'unionid' => 'max:28'
        , 'avatar' => 'max:255'
        , 'state' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        'user_name.require' => '用户名不能为空！'
        , 'user_name.max' => '用户名不能超过10个字！'
        , 'tel.require' => '手机号不能为空！'
        , 'tel.unique' => '手机号已被使用！'
        , 'tel.max' => '手机号不能超过11字！'
        , 'card_type.number' => '证件类型的值必须为数字！'
        , 'card_no.max' => '证件号不能超过32字！'
        , 'sex.in' => '性别的值不正确！'
        , 'state.in' => '登录状态数值不正确！'
    ];

    protected $scene = [
        'save' => [
            'user_name', 'user_pwd', 'tel', 'card_type', 'card_no'
            , 'sex', 'province', 'city', 'district', 'address', 'bank', 'bank_no'
            , 'email' , 'describe', 'openid', 'unionid', 'avatar', 'state'
        ]
    ];


    /**
     * 校验密码
     * @param $value
     * @param $rule
     * @param $data
     * @return bool|string
     */
    protected function checkPwd($value, $rule, $data)
    {
        //添加用户时，新密码必填
        if (empty($data['user_id']) && empty($data['user_pwd'])) {
            return '密码不能为空';
        }

        //编辑用户时，验证确认密码
        if (!empty($data['user_pwd'])) {
            if (empty($data['confirm']) || ($data['user_pwd'] != $data['confirm'])) {
                return '两次输入的密码不一致';
            }

            if (!password_strength($data['user_pwd'])) {
                return '密码太简单，请重新修改';
            }
        }
        return true;
    }
}