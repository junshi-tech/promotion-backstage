<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\BasicMenu as BasicMenuModel;
use app\admin\validate\BasicMenu as BasicMenuValidate;
use think\Db;
use think\facade\Session;

class Menu extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new BasicMenuModel();
        $this->currentValidate = new BasicMenuValidate();
    }

    /**
     * 获取菜单列表（左侧）
     * @return array
     */
    public function getDataToLeft()
    {
        $map = [];
        $map[] = ["display", '=', 1];
        $list = $this->currentModel->getDataTree($map);
        $this->result['data'] = $list;
        return $this->result;
    }

    /**
     * 获取菜单列表（数据列表页）
     * @return array
     */
    public function getMenuList()
    {
        $map = $this->getDataMap();
        $list = $this->currentModel->getDataTableTree($map);
        $menu_list = Db::name('basic_menu')->column('menu_name', 'menu_id');
        foreach ($list as $k=>$v) {
            $list[$k]['pid_text'] = $v['pid'] == 0 ? '顶级' : ($menu_list[$v['pid']] ?? '');
            unset($list[$k]['child']);
        }

        $this->result['data'] = $list;
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array|bool
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['menu_name'])) {
            $map[] = ['menu_name', 'like', '%'.$this->data['menu_name'].'%'];//菜单名称
        }
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $menu_id
     * @return array
     */
    public function edit($menu_id = 0)
    {
        $data = $this->currentModel->where('menu_id', $menu_id)->find();

        $pid = $data['pid'] ?? 0;
        $map = [];
        if (get_aid() != 1) {
            $map[] = ['only_admin', '=', 0];
        }
        $map[] = ['menu_id', '<>', $menu_id];
        $data['pid_list'] = $this->currentModel->getMenuOptionTree($pid, $map);//获取上级菜单
        $this->result['data'] = $data;
        return $this->result;
    }


}



