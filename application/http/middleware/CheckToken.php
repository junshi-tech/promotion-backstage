<?php

namespace app\http\middleware;

use think\Db;
use think\facade\Cache;
use think\facade\Session;

class CheckToken
{
    public function handle($request, \Closure $next)
    {
        try {
            if (!$request->param('token') && !$request->header('token')) {
                exception('token不能为空！');
            }
            $token = !empty($request->param('token')) ? $request->param('token') : $request->header('token');
            $ip = $request->ip(1) ?? '';
            if (!($userId = Cache::get($token . $ip))) {
                exception('无效的token');
            }
            $userInfo = Db::name('user')->where('user_id', $userId)->field('user_id,nickname,phone')->find();
            if (!$userInfo) {
                exception('查找不到用户信息');
            }
            //设置session
            Session::set('userInfo', $userInfo);
            //刷新token缓存时间
            Cache::set($token, $userInfo['user_id'], config('api.tokenValidTime'));

            return $next($request);
        } catch (\Exception $e) {
            //errorCode 值为  10000 时， 前端将重新请求 获取 oken 接口，此错误码不可更改和重用
            die(json_encode(['code' => 0, 'errorCode' => 10000, 'msg' => $e->getMessage()]));
        }

    }

}
