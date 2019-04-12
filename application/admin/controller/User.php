<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\User as UserModel;
use app\admin\model\UserRole;
use app\admin\validate\User as UserValidate;

class User extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new UserModel();
        $this->currentValidate = new UserValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->where($map)->field('user_id,user_name,tel,describe,create_time')->paginate($this->limit)->toArray();

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
     * @param int $user_id
     * @return array
     */
    public function edit($user_id = 0)
    {
        $data = $this->currentModel->where('user_id', $user_id)->field('user_pwd', true)->find();

        $UserRole = new UserRole();
        $data['role_list'] = $UserRole->getRoleList();

        $this->result['data'] = $data;
        return $this->result;
    }



}