<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\AdminRole as AdminRoleModel;
use app\admin\validate\AdminRole as AdminRoleValidate;
use think\Db;

class AdminRole extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new AdminRoleModel();
        $this->currentValidate = new AdminRoleValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->where($map)->field('role_id,role_name,describe,create_time')->order('sort_num asc')->paginate($this->limit)->toArray();
        $this->result['count'] = $res['total'];
        $this->result['data'] = $res['data'];
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['role_name'])) {
            $map[] = ['role_name', 'like', '%'.$this->data['role_name'].'%'];//部门名称
        }
        if (!empty($this->data['pid'])) {
            $map[] = ['pid', '=', $this->data['pid']];//上级部门
        }
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $role_id
     * @return array
     */
    public function edit($role_id = 0)
    {
        $data = $this->currentModel->where('role_id', $role_id)->find();

        $this->result['data'] = $data ?? [];
        return $this->result;
    }

    /**
     * 获取菜单列表（角色编辑页）
     * @param $role_id
     * @return array
     */
    public function getMenuList($role_id = 0)
    {
        $list = Db::name('basic_menu')->where('only_admin', 0)->field('menu_id,pid,menu_name,sort_num,url,describe')->order('sort_num asc')->select();
        $auth = Db::name('user_role')->where('role_id', $role_id)->value('auth');
        $auth_arr = explode(',', $auth);
        $list = \Tree::get_Table_tree($list, 'menu_name', 'menu_id');
        $menu_list = Db::name('basic_menu')->column('menu_name', 'menu_id');
        foreach ($list as $k=>$v) {
            $list[$k]['pid_text'] = $v['pid'] == 0 ? '顶级' : ($menu_list[$v['pid']] ?? '');
            $list[$k]['LAY_CHECKED'] = in_array($v['menu_id'], $auth_arr) || $v['menu_id'] == 1 ? true : false;
            unset($list[$k]['child']);
        }

        $this->result['data'] = $list;
        return $this->result;
    }

}



