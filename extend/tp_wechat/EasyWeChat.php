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
use think\facade\Request;

class EasyWeChat
{
    private $easyWechat;

    public function __construct()
    {
        $this->initEasyWechat();
    }

    /**
     * 实例化
     */
    public function initEasyWechat()
    {
        $config = $this->initConfig();
        $this->easyWechat = Factory::officialAccount($config);
    }

    /**
     * 初始化配置信息
     * @return mixed
     */
    public function initConfig()
    {
        $domain = get_domain();
        if ($domain == 'http://www.szsjunshi.com') {
            //生产环境
            $config = Config::get('wechat.official_account.default');
        } else {
            //测试环境
            $config = Config::get('wechat.official_account_test.default');
        }

        $back_url = Request::param('back_url');
        if (!empty($back_url)) {
            $config['oauth']['callback'] .= '?back_url='.urlencode($back_url);
        }

        return $config;
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