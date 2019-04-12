<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2019} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/10 13:51
// +----------------------------------------------------------------------

namespace tp_wechat;

use EasyWeChat\Factory;
use think\facade\Config;

class EasyWeChat
{
    //微信配置
    private $config;
    private $easyWechat;

    public function __construct()
    {
//        $domain = Request::domain();
//        if (strpos($domain, 'ts-www') !== false) {
//            //生产环境
//            $this->config = Config::get('wechat.official_account.default');
//            ;
//        } else {
//            //测试环境
//            $this->config = Config::get('wechat.official_account_test.default');
//        }

        //暂时用测试环境
        $this->config = Config::get('wechat.official_account_test.default');
        $this->easyWechat = Factory::officialAccount($this->config);
    }

    /**
     * 获取EasyWechat实例
     * @return \EasyWeChat\OfficialAccount\Application
     */
    public function getEasyWechat()
    {
        return $this->easyWechat;
    }

    /**
     * 给某一用户发送公众号消息
     * @param $openid
     * @param $tplid
     * @param $url
     */
    public function sendMsg($openid, $tplid, $url)
    {
        $this->easyWechat->template_message->send([
            'touser' => $openid,
            'template_id' => $tplid,
            'url' => $url,
            'data' => [
                'first' => '恭喜你中奖了',
                'keyword1' => '招商会',
                'keyword2' => ['一等奖', '#FF5722'], // 指定为红色,
                'keyword3' => '2014年7月21日 18:36',
                'remark' => '请尽快到主席台领取相关奖品',
            ],
        ]);
    }
}