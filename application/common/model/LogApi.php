<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/2/26 17:44
// +----------------------------------------------------------------------

namespace app\common\model;

use think\facade\Request;
use think\Model;

class LogApi extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';

    /**
     * 保存日志
     * @param string $remark
     */
    public static function record($remark = '')
    {
        $uid = !empty(get_uid()) ? get_uid() : 0;
        $url = Request::url(true);
        $module = Request::module();
        $controller = Request::controller();
        $action = Request::action();
        $param = Request::param() ? json_encode(Request::param()) : '';
        self::create([
            'user_id'  => $uid,
            'url'    => $url,
            'module'    => $module,
            'controller'    => $controller,
            'action'    => $action,
            'params'     => $param,
            'remark'     => $remark,
            'ip'        => Request::ip(1),
            'user_agent' => Request::server('HTTP_USER_AGENT')
        ]);
    }
}