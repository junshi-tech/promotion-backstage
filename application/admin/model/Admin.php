<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Admin as CoreAdmin;
use think\Db;

class Admin extends CoreAdmin
{
    /**
     * 获取人员列表
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getUserList($map = [])
    {
        return Db::name('admin')->where($map)->field('admin_id,tel,user_name')->select();
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array
     */
    public function handleSaveData($data = []){
        if (!empty($data['admin_id']) && empty($data['user_pwd'])) {
            unset($data['user_pwd']);
        }
        return $data;
    }



}