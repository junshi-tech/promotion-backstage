<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\BasicBanner as BasicBannerModel;
use app\admin\validate\BasicBanner as BasicBannerValidate;
use app\admin\model\BasicInfo;
use think\Db;
use think\facade\Session;

class Banner extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new BasicBannerModel();
        $this->currentValidate = new BasicBannerValidate();
    }

    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $list = $this->currentModel->where($map)->field('banner_id,location,img_url,img_name,describe,link,state,create_time')
            ->append(['state_text', 'img_url_full'])->paginate($this->limit)->toArray();

        $basic_pid = Db::name('basic_info')->where('basic_code', 'banner')->value('basic_id');
        $basic_list = Db::name('basic_info')->where('pid', $basic_pid)->column('basic_name', 'basic_id');
        foreach ($list['data'] as $k=>$v) {
            $list['data'][$k]['location'] = $basic_list[$v['location']] ?? '';
        }
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
        if (!empty($this->data['location'])) {
            $map[] = ['location', '=', $this->data['location']];//位置
        }
        if (!empty($this->data['img_name'])) {
            $map[] = ['img_name','like', '%'.$this->data['img_name'].'%'];//手机号
        }
        if (!empty($this->data['create_begin']) && !empty($this->data['create_end'])) {
            $map[] = ['create_time', 'between time', [$this->data['create_begin'].' 00:00:00',$this->data['create_end'].' 23:59:59']];//注册日期
        }
        $map[] = ['language', '=', Session::get('system_config.language') ?? 1];
        return $map;
    }

    /**
     * 添加|编辑
     * @param int $banner_id
     * @return array
     */
    public function edit($banner_id = 0)
    {
        $data = [];
        if ($banner_id) {
            $data = $this->currentModel->where('banner_id', $banner_id)->find();
        }

        $BasicInfo = new BasicInfo();
        $data['locationList'] = $BasicInfo->getBasicList('banner');
        $this->result['data'] = $data;
        return $this->result;
    }










}



