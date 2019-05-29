<?php


namespace app\soldier\controller;

use app\common\controller\Log;
use app\soldier\model\PicSoldier as PicSoldierModel;
use app\soldier\validate\PicSoldier as PicSoldierValidate;
use think\Db;

class PicSoldier extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new PicSoldierModel();
        $this->currentValidate = new PicSoldierValidate();
    }

    public function getData($id)
    {
        $data = $this->currentModel->where('id', $id)->find();

        $this->result['data'] = $data;
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
            if (!empty($this->data['img_url'])) {
                $this->data['img_url'] = json_encode($this->data['img_url']);
            }
            $this->data['user_id'] = $this->user_id;

            //如果该用户已生成，则更新
            $id = Db::name('pic_soldier')->where('user_id', $this->user_id)->value('id');
            if($id) $this->data['id'] = $id;

            //保存数据
            $this->currentModel->save($this->data);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            (new Log())->saveErrorLog($msg . ' [' . $e->getFile() . ':' . $e->getLine() . ']');
            $this->result['code'] = 0;
            $this->result['msg'] = $msg;
            return $this->result;
        }
        $pk = $this->currentModel->getPk();
        $this->result['data']['soldier_id'] = $this->currentModel->getData($pk);
        return $this->result;
    }

    /**
     * 保存点赞信息
     * @param $soldier_id
     * @return array
     */
    public function saveLike($soldier_id)
    {
        $count = Db::name('pic_soldier_like')->where('soldier_id', $soldier_id)->where('user_id', $this->user_id)->count();

        if ($count == 0) {
            $save_data = [];
            $save_data['soldier_id'] = $soldier_id;
            $save_data['user_id'] = $this->user_id;
            $save_data['remark'] = $this->data['remark'] ?? '';
            $save_data['create_time'] = time();
            Db::name('pic_soldier_like')->insert($save_data);
        } else {
            $this->result['code'] = 0;
            $this->result['msg'] = '点赞信息已存在，请勿重复操作！';
        }
        return $this->result;
    }

    /**
     * 获取参与人数
     */
    public function getJoinNum()
    {
        $base_num = 5000;
        $count = $this->currentModel->count();
        $this->result['data']['number'] = $base_num + $count;
        return $this->result;
    }

    /**
     * 获取排行榜
     * @return array
     * @throws \think\exception\DbException
     */
    public function getRanking()
    {
        $soldier_list = Db::name('pic_soldier')->column('username', 'id');
        $soldier_like = Db::name('pic_soldier_like')->field('soldier_id,count(*) as number')->group('soldier_id')->order('number desc')->limit(100)->select();
        foreach ($soldier_like as $k=>$v) {
            $soldier_like[$k]['username'] = $soldier_list[$v['soldier_id']];
        }

        $this->result['data'] = $soldier_like;
        return $this->result;
    }



}