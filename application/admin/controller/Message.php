<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\UserMessage;
use think\Db;
use think\facade\Session;

class Message extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new UserMessage();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->alias('me')
            ->join('UserMessageList li', 'li.msg_id=me.id')
            ->field('li.id as list_id,li.msg_id,li.user_id,li.is_read,me.title,me.content,me.url,me.create_time')
            ->where($map)->order('li.id desc')->paginate($this->limit);

        $this->result['count'] = $res->total();
        $this->result['data'] = $res->items();
        return $this->result;

    }

    /**
     * 获取数据列表，搜索条件
     * @return array
     */
    public function getDataMap()
    {
        if (!empty($this->data['type'])) {
            $map['type'] = $this->data['type'];//类型
        }
        $map['li.user_id'] = get_aid();
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $id
     * @return array
     */
    public function edit($id = 0)
    {
        $data = $this->currentModel->where('id', $id)->find();

        $this->result['data'] = $data ?? [];
        return $this->result;
    }

    /**
     * 添加|编辑
     * @param int $list_id
     * @return array
     */
    public function detail($list_id = 0)
    {
        $data = Db::name('user_message')->alias('me')
            ->join('UserMessageList li', 'li.msg_id=me.id')
            ->field('li.id as list_id,me.title,me.content,me.url,me.create_time')
            ->where('li.id', $list_id)->order('li.id desc')->find();

        Db::name('UserMessageList')->where('id', $list_id)->update(['is_read' => 1]);//更新为已读
        $this->result['data'] = $data ?? [];
        return $this->result;
    }

    /**
     * 获取未读信息数量
     * @return array
     */
    public function getNewsCount()
    {
        $map = [];
        $map['is_read'] = 0;
        $map['user_id'] = get_aid();
        $ids = Db::name('UserMessageList')->where($map)->column('msg_id');//获取未读消息数量
        $count = count($ids);
        $type = $this->data['type'] ?? '';
        if ($type == 'list') {
            $public_count = Db::name('UserMessage')->whereIn('id', $ids)->where('type', 1)->count();//获取公告数量
            $this->result['data'] = [
                'all_count' => $count,
                'all_html' => $count > 0 ? '<span class="layui-badge">'. $count .'</span>' : '',
                'public_html' => $public_count > 0 ? '<span class="layui-badge">'. $public_count .'</span>' : '',
                'private_html' => $count - $public_count > 0 ? '<span class="layui-badge">'. ($count - $public_count) .'</span>' : '',
            ];
        } else {
            $this->result['data'] = [
                'all_count' => $count,
            ];
        }

        return $this->result;
    }

}



