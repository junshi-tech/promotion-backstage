<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/4/8 17:08
// +----------------------------------------------------------------------

namespace app\soldier\controller;

use app\common\controller\Base as CoreBase;
use tp_wechat\EasyWeChat;

class Base extends CoreBase
{
    /**
     * 接收参数
     * @var
     */
    protected $data;

    /**
     * 当前登录用户id
     * @var
     */
    protected $user_id;

    /**
     * 返回参数
     * @var array
     */
    protected $result = [
        'code' => 1, //成功1，失败0
        'msg' => 'success',
        'data' => []
    ];
    /**
     * easyWechat 实例
     * @var \EasyWeChat\OfficialAccount\Application
     */
    protected $easyWechat;

    public function __construct()
    {
        parent::__construct();
        $this->data = $this->request->param();
        $this->user_id = session('userInfo.user_id');
        $this->easyWechat = (new EasyWeChat())->getEasyWechat();
    }

}