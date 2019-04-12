<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:11
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\model\LogDataChange;
use think\Db;

class Trash extends Base
{
    /**
     * 初始化模型、验证器
     */
    public function __construct()
    {
        parent::__construct();
        $this->currentModel = new LogDataChange();
    }

    /**
     * 获取数据列表
     * @return array
     */
    public function getData()
    {
        $map = $this->getDataMap();//获取搜索条件
        $res = $this->currentModel->where($map)->order('id desc')->paginate($this->limit)->toArray();

        $this->result['count'] = $res['total'];
        $this->result['data'] = $this->currentModel->formatData($res['data']);
        return $this->result;

    }

    /**
     * 获取数据列表，搜索条件
     * @return array
     */
    public function getDataMap()
    {
        $map = [];
        if (!empty($this->data['id'])) {
            $map[] = ['id', '=', $this->data['id']];//主键id
        }
        if (!empty($this->data['table_name'])) {
            $map[] = ['table_name', '=', $this->data['table_name']];//数据表
        }
        if (!empty($this->data['create_begin']) && !empty($this->data['create_end'])) {
            $map[] = ['create_time', 'between time', [$this->data['create_begin'].' 00:00:00',$this->data['create_end'].' 23:59:59']];//删除时间
        }
        $map[] = ['state', '=', 1];//有效数据
        $map[] = ['type', '=', 3];//类型：删除
        return $map;
    }

    /**
     * 获取表名列表
     * @return array
     */
    public function getTableList()
    {
        $table_list = Db::name('log_data_change')->where('type', 3)->group('table_name')->column('table_name');
        $this->result['data'] = $table_list;
        return $this->result;
    }

    /**
     * 还原
     * @param $id
     */
    public function recover($id)
    {
        $data = Db::name('log_data_change')->whereIn('id', $id)->field('table_name,content')->select();
        if (empty($data)) {
            $this->error('查找不到需要还原的数据!');
        }
        try {
            foreach ($data as $k=>$v) {
                $v['content'] = json_decode($v['content'], true);
                Db::name($v['table_name'])->insert($v['content']);
            }
            Db::name('log_data_change')->whereIn('id', $id)->delete();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }

        $this->success('还原成功!');
    }

    /**
     * 彻底删除，state置为0
     * @param $id
     */
    public function delete($id)
    {
        $this->currentModel->whereIn('id', $id)->update(['state'=>0]);
        $this->success('删除成功!');
    }

}



