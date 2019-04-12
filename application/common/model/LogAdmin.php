<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/2/26 17:44
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Db;
use think\facade\Request;
use think\Model;

class LogAdmin extends Model
{
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'create_time';

    /**
     * 保存日志
     * @param string $title
     * @param string $content
     */
    public static function record($title = '', $content = '')
    {
        $aid = get_aid();
        $username = Db::name('admin')->where('admin_id', $aid)->value('user_name');
        $module = Request::module();
        $url = Request::url(true);
        $action = Request::action();
        $param = Request::param() ? json_encode(Request::param()) : '';
        self::create([
            'admin_id'  => $aid,
            'admin_name'  => $username,
            'url'    => $url,
            'module'    => $module,
            'action'    => $action,
            'param'     => $param,
            'title'     => $title,
            'content'   => $content,
            'ip'        => Request::ip(1),
            'user_agent' => Request::server('HTTP_USER_AGENT')
        ]);
    }
}