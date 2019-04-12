<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\AdminDept as AdminDeptModel;
use app\admin\validate\AdminDept as AdminDeptValidate;
use think\Db;

class AdminDept extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new AdminDeptModel();
        $this->currentValidate = new AdminDeptValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $data = $this->currentModel->where($map)->field('dept_id,dept_name,pid,create_time')->order('sort_num asc')->select()->toArray();
        $dept_list = Db::name('admin_dept')->column('dept_name','dept_id');
        foreach ($data as $k=>$v) {
            $data[$k]['pid_text'] = $v['pid'] == 0 ? '顶级' : ($dept_list[$v['pid']] ?? '');
        }

        $this->result['data'] = \Tree::get_Table_tree($data, 'dept_name', 'dept_id');
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['dept_name'])) {
            $map[] = ['dept_name', 'like', '%'.$this->data['dept_name'].'%'];//部门名称
        }
        if (!empty($this->data['pid'])) {
            $map[] = ['pid', '=', $this->data['pid']];//上级部门
        }
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $dept_id
     * @return array
     */
    public function edit($dept_id = 0)
    {
        $data = $this->currentModel->where('dept_id', $dept_id)->find();

        $pid = $data['pid'] ?? 0;
        $data['pid_list'] = $this->currentModel->getDeptOptionTree($pid);//获取上级部门
        $this->result['data'] = $data;
        return $this->result;
    }

    /**
     * 获取部门列表
     * @return array
     */
    public function getDeptList()
    {
        $this->result['data'] = $this->currentModel->getDeptOptionTree();
        return $this->result;
    }

}



