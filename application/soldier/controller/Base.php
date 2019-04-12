<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/4/8 17:08
// +----------------------------------------------------------------------

namespace app\soldier\controller;

use tp_wechat\EasyWeChat;

class Base
{

    /**
     * easyWechat 实例
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $easyWechat;

    public function __construct()
    {
        $this->easyWechat = (new EasyWeChat())->getEasyWechat();
    }

}