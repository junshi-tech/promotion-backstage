<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\User as CoreUser;
use think\Db;

class User extends CoreUser
{

    /**
     * 获取人员列表
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserList($map = [])
    {
        return Db::name('user')->where($map)->field('user_id,tel,user_name')->select();
    }

}