<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\BasicSystem as BasicSystemModel;
use app\admin\validate\BasicSystem as BasicSystemValidate;
use app\common\controller\Log;

class System extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new BasicSystemModel();
        $this->currentValidate = new BasicSystemValidate();
    }

    public function getData()
    {
        $list = $this->currentModel->getDataList();
        $this->result['data'] = $list;
        return $this->result;
    }

    /**
     * 保存
     */
    public function save()
    {
        //验证数据
        $this->currentValidate->checkData('save');

        try {
            //处理数据
            $this->data = $this->currentModel->handleSaveData($this->data);

            //保存数据
            $this->currentModel->saveAll($this->data);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            (new Log())->saveErrorLog($msg.' ['.$e->getFile().':'.$e->getLine().']');
            $this->error($msg);
        }
        $this->success('保存成功！');
    }

}



