<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/7 16:56
// +----------------------------------------------------------------------

namespace app\soldier\controller;

use think\Db;
use think\facade\Cache;
use think\facade\Request;
use tp_wechat\EasyWeChat;

class WeChat extends Base
{

    public function oauth()
    {
        $this->easyWechat->oauth->scopes(['snsapi_userinfo'])->redirect()->send();
    }

    /**
     * 授权回调
     */
    public function oauthCallback()
    {
        try {
            // 获取 OAuth 授权结果用户信息
            $oauth = $this->easyWechat->oauth;
            $wx_user = $oauth->user()->toArray();

            $back_url = Request::param('back_url');

            if (empty($back_url)) {
                throw new \Exception('回调地址back_url不能为空！');
            }

            //保存授权用户信息
            $this->saveUser($wx_user['original']);

            //创建新token
            $token = $this->createToken($wx_user['id']);
            //在跳转到前端地址中加入token
            $back_url .= '?token='.$token;
            header('location:' . $back_url);
            exit;
        }
        catch (\Exception $e) {
            $this->result['code'] = 0;
            $this->result['msg'] = $e->getMessage();
            return $this->result;
        }
    }

    /**
     * 创建token
     * @param string $userId
     * @return string
     */
    public function createToken(string $userId): string
    {
        $key = md5(str_shuffle($userId . time()));
        $ip = Request::ip(1) ?? '';
        Cache::set($key . $ip, $userId, config('api.tokenValidTime'));
        return $key;
    }

    /**
     * 根据用户 user_id 生成token，测试用
     * @param string $userId
     * @return array
     * @throws \think\exception\DbException
     */
    public function getTestToken(string $userId)
    {
        $count = Db::name('user')->where('user_id', $userId)->count();
        if ($count == 0) {
            $this->result['code'] = 0;
            $this->result['msg'] = '用户不存在：' . $userId;
            return $this->result;
        }
        $this->result['data']['token'] = $this->createToken($userId);
        return $this->result;
    }

    /**
     * 保存用户信息
     * @param $wx array 微信用户信息
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function saveUser($wx)
    {
        $data = [];
        $data['openid'] = $wx['openid'];
        $data['unionid'] = $wx['unionid'] ?? '';
        $data['nickname'] = $wx['nickname'];
        $data['city'] = $wx['city'];
        $data['province'] = $wx['province'];
        $data['country'] = $wx['country'];
        $data['headimgurl'] = $wx['headimgurl'];
        $count = Db::name('user')->where('openid', $wx['openid'])->count();
        if ($count > 0) {
            Db::name('user')->where('openid', $wx['openid'])->update($data);
        } else {
            $data['user_id'] = get_uuid();
            Db::name('user')->insert($data);
        }
    }

    /**
     * 获取分享配置
     */
    public function getShareConfig($url)
    {
        $api_list = [
            'updateAppMessageShareData',
            'updateTimelineShareData',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareQZone'
        ];

        //设置分享链接
        $this->easyWechat->jssdk->setUrl($url);
        //获取分享配置
        $jsConfig = $this->easyWechat->jssdk->buildConfig($api_list, false);
        return ['code'=>1, 'msg'=>'获取成功！', 'data'=>json_decode($jsConfig)];
    }

    /**
     * 验证配置信息，接收 & 回复微信消息
     */
    public function server()
    {

        $this->easyWechat->server->push(function ($message) {
            switch ($message['MsgType']) {
                case 'text':
                    return $this->returnByText($message['Content']);
                    break;
                case 'event':
                    return '收到事件消息';
                    break;
                case 'image':
                    return $this->returnByImg($message);
                    break;
                case 'link':
                    return $this->returnByText($message);
                    break;
                default:
                    return '抱歉，未能理解您的问题！';
                    break;
            }
        });

        $response = $this->easyWechat->server->serve();

        //响应输出
        $response->send();
        exit;
    }


    /**
     * 用户发文本消息，处理后进行回复
     * @param $content string
     * @return mixed
     */
    protected function returnByText($content)
    {
        return $content;
    }

    /**
     * 用户发图片，处理后进行回复
     * @param $content string
     * @return mixed
     */
    protected function returnByEvent($content)
    {
        return $content;
    }

    /**
     * 用户发图片，处理后进行回复
     * @param $content string
     * @return mixed
     */
    protected function returnByImg($content)
    {
        return $content['PicUrl'];
    }

    /**
     * 用户发图片，处理后进行回复
     * @param $content string
     * @return mixed
     */
    protected function returnByLink($content)
    {
        return $content;
    }


    public function tsSendMsg()
    {
        $EasyWeChat = new EasyWeChat();
        $EasyWeChat->sendMsg('oPthy55wIachJ-C9YkCmKHPy8gt4', 'Zu7NqPfWOLGBA_KA5yk8fX0xivskvjjMaRpm8O0Qkzc', 'weixin.sogou.com');
    }


}