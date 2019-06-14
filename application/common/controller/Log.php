<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\controller;

use think\Db;
use think\facade\Request;
use think\facade\Session;

class Log
{
    /**
     * 保存定时任务日志
     * @param $remark string 备注
     * @param $is_success int 是否成功
     * @param $task_name string 任务名|方法名
     */
    public function saveTaskLog($remark, $is_success = 1, $task_name = '')
    {
        try{
            $request = Request::instance();
            $data['url'] =  $request->url(true);
            $data['ip'] =  !empty($request->ip(0)) ? $request->ip(0) : 0;
            $data['referer'] =  !empty($request->server('HTTP_REFERER')) ? $request->server('HTTP_REFERER') : '';
            $data['user_agent'] =  !empty($request->header('user-agent')) ? $request->header('user-agent') : '';
            $data['task_name'] = $task_name;
            $data['remark'] = $remark;
            $data['is_success'] = $is_success;
            $data['create_time'] = date('Y-m-d H:i:s');
            Db::name('log_task')->insert($data);
        }
        catch (\Exception $e) {
            $this->saveTaskLog($e->getMessage());
        }
    }

    /**
     * 保存访问日志
     */
    public function saveVisitLog()
    {
        try {
            $request = Request::instance();
            $data = [];
            $data['user_id'] = Request::param('user_id') ?? (get_uid() ?? 0);
            $data['url'] = Request::param('url') ?? $request->url(true);
            $data['ip'] = !empty($request->ip(1)) ? $request->ip(1) : 0;
            $data['number'] = 1;
            $data['create_time'] = time();

            $map = [];
            $map[] = ['ip', '=', $data['ip']];
            $map[] = ['url', '=', $data['url']];
            $map[] = ['create_time', '>', strtotime(date('Y-m-d 00:00:00'))];
            $id = Db::name('log_api')->where($map)->value('id');

            if (!empty($id)) {
                Db::name('log_api')->where('id', $id)->update(['number'=>Db::raw('number+1')]);
            } else {
                Db::name('log_api')->insert($data);
            }

        } catch (\Exception $e) {
            $this->saveErrorLog($e->getMessage().' ['.$e->getFile().':'.$e->getLine().']');
        }
    }

    /**
     * 增加文章阅读数
     * @param string $table_name
     * @return array|bool
     */
    public function addReadNum($table_name = '')
    {
        $table_name_list = ['article', 'goods'];
        if (!in_array($table_name, $table_name_list)) {
            return false;
        }

        $request = Request::instance();
        $id = !empty($request->param('id')) ? $request->param('id') : 0;
        if (empty($id)) {
            return false;
        }

        $pk = Db::name($table_name)->getPk();
        $map = [];
        $map['module'] = 'index';
        $map['controller'] = $table_name == 'goods' ? 'Goods' : 'News';//控制器
        $map['action'] = 'detail';
        $map['params'] = json_encode($request->param());
        $map['ip'] = !empty($request->ip(1)) ? $request->ip(1) : 0;
        $number = Db::name('log_api')->where($map)->value('number');
        if ($number < 2) {
            Db::name($table_name)->where($pk, $id)->update(['read_num'=>Db::raw('read_num+1')]);
        }
        return true;
    }

    /**
     * 保存错误日志
     * @param string $content
     * @return bool
     */
    public function saveErrorLog($content = '')
    {
        $data = [];
        $data['user_id'] = get_uid() ?? 0;
        $data['url'] =  Request::url(true);
        $data['method'] = Request::method();
        $data['params'] = json_encode(Request::param(), JSON_UNESCAPED_UNICODE);
        $data['content'] = $content;
        $data['create_time'] = date('Y-m-d H:i:s');
        Db::name('log_error')->insertGetId($data);
        return true;
    }

    /**
     * 发送站内信
     * @param array $data
     * @return array|bool
     */
    public function sendMessage($data = [])
    {
        if (empty($data['user_id']) && empty($data['role_id']) && empty($data['dept_id'])) {
            return ['status'=>false, 'msg'=>'请指定接收者'];
        }
        try{
            $map = [];
            if (!empty($data['user_id'])) {
                $map[] = ['user_id', '=', $data['user_id']];
            }
            if (!empty($data['role_id'])) {
                $map[] = ['role_id', '=', $data['role_id']];
            }
            if (!empty($data['dept_id'])) {
                $map[] = ['dept_id', '=', $data['dept_id']];
            }
            $user_ids = Db::name('user')->where($map)->column('user_id');//获取接收者id

            if (empty($user_ids)) {
                return ['status'=>false, 'msg'=>'接收者不明确'];
            }
            $saveData['url'] =  Request::url(true);
            $saveData['title'] = !empty($data['title']) ? $data['title'] : '';
            $saveData['content'] = !empty($data['content']) ? $data['content'] : '';
            $saveData['type'] = !empty($data['type']) ? $data['type'] : 1;//通知类型：1系统消息 ，2系统公告，3新发布
            $saveData['device'] = !empty($data['device']) ? $data['device'] : 0;//设备类型：0不区分，1客户端站内信,2后台站内信
            $saveData['create_by'] = get_uid() ?? 1;
            $saveData['create_time'] = time();

            $id = Db::name('user_message')->insertGetId($saveData);//插入消息主表
            $saveList = [];
            foreach ($user_ids as $k=>$v) {
                $saveList[$k]['user_id'] = $v;
                $saveList[$k]['msg_id'] = $id;
            }
            Db::name('user_message_list')->insertAll($saveList);//插入发送列表
        }
        catch (\Exception $e) {
            $log = [];
            $log['content'] = $e->getMessage().'; '.json_encode($data, JSON_UNESCAPED_UNICODE);
            $log['create_time'] = date('Y-m-d H:i:s');
            Db::name('log_error')->insert($log);
        }
        return true;
    }

}

