<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/27 17:30
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\App;
use think\Controller;
use think\facade\Hook;

abstract class Base extends Controller
{
    /**
     * 当前模型
     * @var
     */
    protected $currentModel;

    /**
     * 当前验证器
     * @var
     */
    protected $currentValidate;

    /**
     * Base constructor.
     * @param App|null $app
     */
    public function __construct(App $app = null)
    {
        parent::__construct($app);
        //添加行为标签位，触发自动执行
        Hook::listen("controller_init");
    }

}