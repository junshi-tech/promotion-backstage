<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\BasicInfo as BasicInfoModel;
use app\admin\validate\BasicInfo as BasicInfoValidate;

class BasicInfo extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new BasicInfoModel();
        $this->currentValidate = new BasicInfoValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $list = $this->currentModel->where($map)->field('basic_id,pid,basic_code,basic_name,sort_num,describe,state')
            ->append(['pid_text'])->order('pid,sort_num asc')->paginate($this->limit)->toArray();

        $this->result['count'] = $list['total'];
        $this->result['data'] = $list['data'];
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['pid'])) {
            $ids = get_child_by_tree($this->data['pid'], 'basic_info', false);
            $map[] = ['basic_id', 'in', $ids];
        }

        if (!empty($this->data['basic_name'])) {
            $map[] = ['basic_name', 'like', '%'.$this->data['basic_name'].'%'];
        }
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $basic_id
     * @return array
     */
    public function edit($basic_id = 0)
    {
        $data = $this->currentModel->where('basic_id', $basic_id)->find();

        $pid = $data['pid'] ?? 0;
        $map = [];
        $map[] = ['basic_id', '<>', $basic_id];
        $data['pid_list'] = $this->currentModel->getBasicOptionTree($pid, $map);//获取上级菜单
        $this->result['data'] = $data;
        return $this->result;
    }



}



