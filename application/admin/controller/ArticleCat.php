<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\ArticleCat as ArticleCatModel;
use app\admin\validate\ArticleCat as ArticleCatValidate;
use app\admin\model\BasicInfo as BasicInfoModel;
use app\admin\model\Article as ArticleModel;
use think\facade\Session;

class ArticleCat extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new ArticleCatModel();
        $this->currentValidate = new ArticleCatValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $list = $this->currentModel->getDataTableTree($map);

        foreach ($list as $key=>$val) {
            unset($list[$key]['child']);
        }

        $this->result['count'] = $this->currentModel->where($map)->count();
        $this->result['data'] = array_slice($list, ($this->page - 1) * $this->limit, $this->limit);
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array|bool
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['cat_type'])) {
            $map[] = ['cat_type', '=', $this->data['cat_type']];//板块
        }
        if (!empty($this->data['cat_name'])) {
            $map[] = ['cat_name','like', '%'.$this->data['cat_name'].'%'];//栏目名称
        }
        $map[] = ['language', '=', Session::get('system_config.language') ?? 1];
        return $map;
    }

    /**
     * 获取版块列表
     */
    public function getCatTypeList()
    {
        $BasicInfo = new BasicInfoModel();
        $this->result['data'] = $BasicInfo->getBasicList('art_group');
        return $this->result;
    }

    /**
     * 添加|编辑
     * @param int $cat_id
     * @return array
     */
    public function edit($cat_id = 0)
    {
        $data = [];
        if ($cat_id) {
            $data = $this->currentModel->where('cat_id', $cat_id)->find()->toArray();
        }

        $BasicInfo = new BasicInfoModel();
        //获取版块列表
        $data['cat_type_list'] = $BasicInfo->getBasicList('art_group');

        //获取栏目下拉列表
        $map = [];
        $curr_cat_id = 0;
        if (!empty($data['cat_id'])) {
            $map['cat_type'] = $data['cat_type'];
            $curr_cat_id = $data['cat_id'];
        }
        $ArticleModel = new  ArticleModel();
        $data['cat_list'] = $ArticleModel->getCatOptionTree($map, $curr_cat_id);

        //显示位置
        $data['location_list'] = $BasicInfo->getBasicList('location');

        $this->result['data'] = $data;
        return $this->result;
    }
}



