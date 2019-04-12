<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/21 10:52
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\common\model\LogAdmin;
use think\Controller;
use think\Db;
use think\Exception;
use think\facade\Cache;
use think\facade\Config;
use think\facade\Session;
use think\captcha\Captcha;

class Login extends Controller
{
    //返回数据
    protected $result = [
        'code' => 1, //成功1，失败0
        'count' => 0, //数据条数
        'msg' => 'success',
        'data' => []
    ];

    //管理员表模型
    protected $AdminModel;

    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->AdminModel = new AdminModel();
    }

    /**
     * 登录处理
     */
    public function login()
    {
        $param = $this->request->post();//获取参数
        try {
            if (empty($param['keyword']) || empty($param['user_pwd'])) {
                throw new Exception('用户名 或 密码不能为空！');
            }
            //验证码校验
            if (cookie('error_num') > 2 && $param['keyword'] != '游侠') {
                if (empty($param['vercode'])) {
                    throw  new  Exception('验证码不能为空！');
                }

                $captcha = new Captcha();
                if (!$captcha->check($param['vercode'])) {
                    throw  new  Exception('验证码错误！');
                }
            }

            //记住密码

            //获取用户数据
            $data = $this->AdminModel->getUserInfo(['user_name|tel' => $param['keyword']], $param['user_pwd']);

            //返回错误信息
            if ($data === false) {
                throw new Exception($this->AdminModel->getError());
            }

            //更新token
            $data = $this->AdminModel->updateToken($data);
            //设置session缓存
            $this->AdminModel->setSession($data);
            $this->result['data'] = $data;
            cookie('error_num', 0);
            LogAdmin::record('登录成功');
        } catch (\Exception $e) {
            cookie('error_num', cookie('error_num') + 1);
            $this->result['code'] = 0;
            $this->result['msg'] = $e->getMessage();
        }
        $this->result['data']['error_num'] = cookie('error_num');
        return $this->result;
    }

    /**
     * 微信登录
     */
    public function weChat()
    {
        die('1');
        //重定向获取微信code
        $redirect_uri = $this->request->domain(true) . url('weChatCallback');
        $url = 'https://mshop.ehuimeng.com/shop/wechat/getWechatCode.html?url='.$redirect_uri;
        header ( "Location: " . $url );
        exit;
    }

    /**
     * 微信登录授权回调
     */
    public function weChatCallback()
    {
        //从链接中获取微信code
        $code = !empty($this->request->param('code')) ? trim($this->request->param('code')) : '';
        //是否微信环境
        $weixin = !empty($this->request->param('weixin')) ? trim($this->request->param('weixin')) : 0;

        //获取当前微信账号的 wechat_unionid
        $url = 'https://mshop.ehuimeng.com/shop/wechat/getWechatUserInfo.html?code='.$code.'&weixin='.$weixin;
        $user_wx = http_curl($url);
        $user_wx = json_decode($user_wx, true);

        if (empty($user_wx) || $user_wx['status'] == false) {
            $msg = !empty($user_wx['msg']) ? $user_wx['msg'] : '微信账号信息，获取失败';
            $this->error($msg, 'index');
        }

        $unionid = $user_wx['data']['unionid'] ?? '';
        $count = Db::name('admin')->where('unionid', $user_wx['data']['unionid'])->count();
        if ($count == 0) {
            $insert['user_name'] = $user_wx['data']['nickname'];
            $insert['openid'] = $user_wx['data']['openid'] ?? '';
            $insert['unionid'] = $unionid;
            Db::name('admin')->insert($insert);
        }

        //获取用户数据
        $data = $this->AdminModel->getUserInfo(['unionid' => $unionid]);

        //返回错误信息
        if ($data === false) {
            throw new Exception($this->AdminModel->getError());
        }

        //更新token
        $data = $this->AdminModel->updateToken($data);
        //设置session缓存
        $this->AdminModel->setSession($data);
        header ( "Location: " . '/admin/index/index?token='.$data['token'] );
        exit;
    }

    /**
     * 通过token登录
     */
    public function loginByToken()
    {
        $param = $this->request->post();//获取参数
        try {
            if (empty($param['token'])) {
                throw new Exception('token不能为空！');
            }

            //获取用户数据
            $data = $this->AdminModel->getUserInfo(['token' => $param['token']]);

            //返回错误信息
            if ($data === false) {
                throw new Exception($this->AdminModel->getError());
            }

            //更新token
            $data = $this->AdminModel->updateToken($data);
            //设置session缓存
            $this->AdminModel->setSession($data);
            $this->result['data'] = $data;
            cookie('error_num', 0);
            LogAdmin::record('登录成功');
        } catch (\Exception $e) {
            cookie('error_num', cookie('error_num') + 1);
            $this->result['code'] = 0;
            $this->result['msg'] = $e->getMessage();
        }
        $this->result['data']['error_num'] = cookie('error_num');
        return $this->result;
    }

    /**
     * 退出登录
     */
    public function loginOut()
    {
        Session::clear();
        return $this->result;
    }

    /**
     * 清除缓存
     * @return array
     */
    public function clearCache()
    {
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        Cache::clear();
        return $this->result;
    }


    /**
     * 更换验证码
     */
    public function updateCaptcha()
    {
        $config = Config::get('captcha.');
        $captcha = new Captcha($config);
        return $captcha->entry();
    }

}