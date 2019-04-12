<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Admin as AdminModel;
use app\admin\validate\Admin as AdminValidate;
use app\admin\model\AdminRole;

class Admin extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new AdminModel();
        $this->currentValidate = new AdminValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->where($map)->field('admin_id,user_name,tel,role_id,dept_id,describe,create_time')
            ->append(['role_name','dept_name'])->paginate($this->limit)->toArray();

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
        if (!empty($this->data['user_name'])) {
            $map[] = ['user_name', 'like', '%'.$this->data['user_name'].'%'];//用户名
        }
        if (!empty($this->data['tel'])) {
            $map[] = ['tel','like', '%'.$this->data['tel'].'%'];//手机号
        }
        if (!empty($this->data['create_begin']) && !empty($this->data['create_end'])) {
            $map[] = ['create_time', 'between time', [$this->data['create_begin'].' 00:00:00',$this->data['create_end'].' 23:59:59']];//注册日期
        }
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $admin_id
     * @return array
     */
    public function edit($admin_id = 0)
    {
        $data = $this->currentModel->where('admin_id', $admin_id)->field('user_pwd', true)->find();
        $AdminRole = new AdminRole();
        $data['role_list'] = $AdminRole->getRoleList();

        $this->result['data'] = $data;
        return $this->result;
    }



}