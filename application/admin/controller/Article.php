<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\Article as ArticleModel;
use app\admin\validate\Article as ArticleValidate;
use app\admin\model\BasicInfo as BasicInfoModel;
use think\Db;
use think\facade\Session;

class Article extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new ArticleModel();
        $this->currentValidate = new ArticleValidate();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        if (empty($this->data['cat_type_code'])) {
            $this->result['code'] = 0;
            $this->result['msg'] = '链接参数错误！';
            return $this->result;
        }
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->where($map)->field('art_id,cat_id,title,img_url,level,read_num,state,public_time')
            ->append(['cat_name','level_text','public_date'])->order('sort_num asc, art_id desc')->paginate($this->limit)->toArray();

        $this->result['count'] = $res['total'];
        $this->result['data'] = $res['data'];
        return $this->result;
    }

    /**
     * 获取数据列表，搜索条件
     * @return array|bool
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['cat_type_code'])) {
            $basic_id = Db::name('basic_info')->where('basic_code', $this->data['cat_type_code'])->cache(60)->value('basic_id');
            $cat_ids = Db::name('article_cat')->where('cat_type', $basic_id)->column('cat_id');
            $map[] = ['cat_id', 'in', $cat_ids];//上级栏目
        }
        if (!empty($this->data['cat_id'])) {
            $cat_ids = Db::name('article_cat')->where('pid', $this->data['cat_id'])->column('cat_id');
            array_push($cat_ids,$this->data['cat_id']);
            $map[] = ['cat_id', 'in', $cat_ids];//上级栏目
        }
        if (!empty($this->data['title'])) {
            $map[] = ['title','like', '%'.$this->data['title'].'%'];//标题
        }
        $map[] = ['language', '=', Session::get('system_config.language') ?? 1];
        return $map;
    }

    /**
     * 获取栏目下拉列表
     */
    public function getCatListOptionTree()
    {
        $list = [];
        try {
            $map = [];
            $curr_cat_id = !empty($this->data['cat_id']) ? $this->data['cat_id'] : 0;
            if (!empty($this->data['basic_code'])) {
                $map['cat_type'] = Db::name('basic_info')->where('basic_code', $this->data['basic_code'])->value('basic_id');
            }
            if (!empty($this->data['cat_type'])) {
                $map['cat_type'] = $this->data['cat_type'];
            }

            $list['table_tree'] = $this->currentModel->getCatTableTree($map);
            $list['option_tree'] = $this->currentModel->getCatOptionTree($map, $curr_cat_id);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->result['data'] = $list;
        return $this->result;
    }

    /**
     * 添加|编辑
     * @param int $art_id
     * @return array
     */
    public function edit($art_id = 0)
    {
        $data = [];
        if ($art_id) {
            $data = $this->currentModel->where('art_id', $art_id)->find()->toArray();
            $data['public_time'] = !empty($data['public_time']) ? date('Y-m-d H:i:s', $data['public_time']) : '';
            $data['cat_type'] = Db::name('article_cat')->where('cat_id', $data['cat_id'])->value('cat_type');
        }

        $BasicInfo = new BasicInfoModel();
        //获取版块下拉列表
        $data['cat_type_list'] = $BasicInfo->getBasicList('art_group');

        /*获取推荐等级*/
        $data['level_list'] = $BasicInfo->getBasicList('art_level');

        //获取栏目下拉列表
        $map = [];
        $curr_cat_id = 0;
        if (!empty($data['cat_id'])) {
            $map['cat_type'] = $data['cat_type'];
            $curr_cat_id = $data['cat_id'];
        }
        $data['cat_list'] = $this->currentModel->getCatOptionTree($map, $curr_cat_id);

        $this->result['data'] = $data;
        return $this->result;
    }
}



