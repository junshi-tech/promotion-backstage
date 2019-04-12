<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/7 16:56
// +----------------------------------------------------------------------

namespace app\soldier\controller;

use tp_wechat\EasyWeChat;

class WeChat extends Base
{

    public function oauth($back_url)
    {
        $this->easyWechat->oauth->redirect()->send();
    }

    /**
     * 授权回调
     */
    public function oauthCallback()
    {
        // 获取 OAuth 授权结果用户信息
        $oauth = $this->easyWechat->oauth;
        $user = $oauth->user();

        //记录已登录状态
        $_SESSION['wechat_user'] = $user->toArray();

        //重定向到前端用户页面
        $targetUrl = empty($_SESSION['target_url']) ? '/' : $_SESSION['target_url'];
        header('location:' . $targetUrl);
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