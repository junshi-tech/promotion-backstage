<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class BasicInfo extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'basic_id';

    /**
     * @param $value
     * @param $data
     * @return mixed|string
     */
    public function getPidTextAttr($value, $data)
    {
        $value = $data['pid'] ?? $value;
        return $value == 0 ? '顶级' : Db::name('basic_info')->where('basic_id', $value)->cache(1)->value('basic_name');
    }

    /**
     * 获取某一资料列表
     * @param $basic_code
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getBasicList($basic_code)
    {
        $basic_pid = Db::name('basic_info')->where('basic_code', $basic_code)->value('basic_id');
        return Db::name('basic_info')->where('pid', $basic_pid)->field('basic_id,basic_name')->order('sort_num')->select();
    }


    /**
     * 获取资料列表
     * @param $value int 默认选中id
     * @param array $map
     * @return string
     */
    public function getBasicOptionTree($value = 0, $map = [])
    {
        $list = Db::name('basic_info')->field('basic_id,pid,basic_name')->where($map)->select();
        return \Tree::get_option_tree($list, $value, 'basic_name', 'basic_id');
    }


    /**
     * 保存前处理数据
     * @param array $data
     * @return array|bool
     */
    public function handleSaveData($data = []){
        $data['pid'] = !empty($data['pid']) ? $data['pid'] : 0 ;

        if (empty($data['basic_code'])) {
            $res = $this->createCode($data['pid']);
            if ($res['code'] != 1) {
                $this->error = $res['msg'];
                return false;
            }
            $data['basic_code'] = $res['data'];
        }

        //顺序按最大值加1
        if (empty($data['sort_num'])) {
            $count = $this->where('pid', $data['pid'])->count();
            $data['sort_num'] = $count+1;
        }
        return $data;
    }

    /**
     * 生成资料代号
     * @param $pid
     * @return array
     */
    protected function createCode($pid)
    {
        if (empty($pid)) {
            return ['code'=>0, 'msg'=>'当前新资料代号无规律可循，请手动输入'];
        }

        $max_code = $this->where('pid', $pid)->order('basic_code desc')->value('basic_code');
        $cat_code = $this->where('basic_id', $pid)->value('basic_code');

        //若上级代号非AA类型
        if (!preg_match('/^[A-Z]{2}/',$cat_code)) {
            //当前没有同级别代号，则初始化为AA
            if (empty($max_code)) {
                return ['code'=>1, 'msg'=>'操作成功', 'data'=>'AA'];
            }

            if (!preg_match('/^[A-Z]{2}/',$max_code)) {
                return ['code'=>0, 'msg'=>'当前新资料代号无规律可循，请手动输入'];
            }
        }

        if (strlen($max_code) == 2) {
            $res = serials_number($max_code, 2 , '');
        } else {
            $res = !empty($max_code) ? ++$max_code : $cat_code.'0001';
        }
        return ['code'=>1, 'msg'=>'操作成功', 'data'=>$res];
    }

}