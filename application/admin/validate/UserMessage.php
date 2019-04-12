<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\validate;

use app\common\validate\Base;

class UserMessage extends Base
{
    /*字段规则*/
    protected $rule = [

    ];

    /*返回错误信息*/
    protected $message = [

    ];

    protected $scene = [
        'save' => ['']
    ];
}