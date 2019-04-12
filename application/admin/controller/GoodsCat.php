<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\GoodsCat as GoodsCatModel;

class GoodsCat extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new GoodsCatModel();
    }

    public function getData()
    {
        $list = $this->currentModel->getDataList();
        $this->result['data'] = $list;
        return $this->result;
    }

}



