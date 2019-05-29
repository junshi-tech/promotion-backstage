<?php

namespace app\http\middleware;

use app\common\controller\Log;
use think\Db;
use think\facade\Cache;
use think\facade\Session;

class CheckToken
{
    public function handle($request, \Closure $next)
    {
        try {
            //检验 token 是否有效
            $errorCode = 10000;
            if (!$request->param('token') && !$request->header('token')) {
                exception('token不能为空！');
            }
            $token = !empty($request->param('token')) ? $request->param('token') : $request->header('token');
            $ip = $request->ip(1) ?? '';
            if (!($userId = Cache::get($token . $ip))) {
                exception('无效的token');
            }

            //检验 user
            $errorCode = 10001;

            $userInfo = Db::name('user')->where('user_id', $userId)->field('user_id,nickname,phone,state')->find();
            if (!$userInfo) {
                exception('查找不到用户信息');
            }
            if ($userInfo['state'] == 0) {
                exception('用户已被禁用！');
            }
            //设置session
            Session::set('userInfo', $userInfo);
            //刷新token缓存时间
            Cache::set($token, $userInfo['user_id'], config('api.tokenValidTime'));

            return $next($request);
        } catch (\Exception $e) {
            //errorCode 值为  10000 时， 前端将重新请求 获取 token 接口，此错误码不可更改和重用
            $msg = $e->getMessage();
            (new Log())->saveErrorLog($msg.' ['.$e->getFile().':'.$e->getLine().']');
            die(json_encode(['code' => 0, 'errorCode' => $errorCode, 'msg' => $e->getMessage()]));
        }

    }

}
