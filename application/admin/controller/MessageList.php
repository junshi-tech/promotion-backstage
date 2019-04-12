<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\UserMessageList;
use think\Db;
use think\facade\Session;

class MessageList extends Base
{
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new UserMessageList();
    }

    /**
     * 设置为已读
     * @return array
     */
    public function setRead()
    {
        $map = [];
        if (!empty($this->data['id'])) {
            $map[] = ['id', '=', $this->data['id']];
        }
        $map[] = ['user_id', '=', get_aid()];
        Db::name('user_message_list')->where($map)->update(['is_read' => 1]);
        return $this->result;
    }

}



