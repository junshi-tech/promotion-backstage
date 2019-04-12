<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/10 16:50
// +----------------------------------------------------------------------
namespace app\common\validate;

use think\facade\Request;
use think\Validate;
use traits\controller\Jump;

class Base extends Validate
{
    use Jump;

    /**
     * 参数校验统一入口方法
     * @param string $scene
     * @param array $rule
     * @return bool
     */
    public function checkData($scene = '', $rule = [])
    {
        $param = Request::param();
        $result = $this->batch()->check($param, $rule, $scene);
        if ($result !== true) {
            $this->error(implode('；', $this->error));
        } else {
            return true;
        }
    }


}