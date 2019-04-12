<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class AdminDept extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'dept_id';

    /**
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getPidTextAttr($value, $data)
    {
        $value = $data['pid'] ?? $value;
        return $value == 0 ? '顶级' : Db::name('admin_dept')->where('dept_id', $value)->cache(1)->value('dept_name');
    }

    /**
     * 获取部门列表
     * @param $value int 默认选中id
     * @return string
     */
    public function getDeptOptionTree($value = 0)
    {
        $list = Db::name('admin_dept')->field('dept_id,pid,dept_name')->select();
        return \Tree::get_option_tree($list, $value, 'dept_name', 'dept_id');
    }
}