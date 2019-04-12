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

class UserRole extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'role_id';

    /**
     * 保存字段“权限明细”
     * @param $value
     * @return mixed
     */
    protected function setAuthAttr($value){
        if(is_array($value)){
            $value = implode(',',$value);
        }
        return $value;
    }

    /**
     * 获取角色权限列表
     * @param $map
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getRoleList($map = [])
    {
        return $this->where($map)->field('role_id,role_name,describe')->order('sort_num')->select();
    }

}