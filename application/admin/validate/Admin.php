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

class Admin extends Base
{
    /*字段规则*/
    protected $rule = [
        'dept_id' => 'number'
        , 'user_name' => 'require|max:10'
        , 'user_pwd' => 'max:32'
        , 'role_id' => 'require|max:20'
        , 'login_rank' => 'in:0,1'
        , 'tel' => 'require|unique:admin|checkPwd|max:11'
        , 'email' => 'max:20'
        , 'describe' => 'max:1000'
        , 'openid' => 'max:28'
        , 'unionid' => 'max:28'
        , 'avatar' => 'max:255'
        , 'state' => 'in:0,1'
    ];

    /*返回错误信息*/
    protected $message = [
        'dept_id.number' => '部门id的值必须为数字！'
        ,'user_name.require' => '用户名不能为空！'
        , 'user_name.max' => '用户名不能超过10个字！'
        , 'role_id.require' => '权限分组不能为空！'
        , 'role_id.max' => '权限分组内容超出范围！'
        , 'login_rank.in' => '数据权限不正确！'
        , 'tel.require' => '手机号不能为空！'
        , 'tel.unique' => '手机号已被使用！'
        , 'tel.max' => '手机号不能超过11字！'
        , 'state.in' => '登录状态数值不正确！'
    ];

    protected $scene = [
        'save' => [
            'dept_id', 'user_name', 'user_pwd', 'role_id', 'login_rank', 'tel'
            , 'email', 'describe', 'openid', 'unionid', 'avatar', 'state'
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
        if (empty($data['admin_id']) && empty($data['user_pwd'])) {
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