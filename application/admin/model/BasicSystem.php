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

class BasicSystem extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'sys_code';

    /**
     * 解析取值范围
     * @param $value
     * @return mixed|string
     */
    public function getValueRangeAttr($value)
    {
        return !empty($value) ? json_decode($value, true) : '';
    }

    /**
     * 获取数据列表,以child分组
     * @return array
     */
    public function getDataList()
    {
        return $this->column('sys_value', 'sys_code');
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array
     */
    public function handleSaveData($data = [])
    {
        $res = [];
        foreach ($data['sys'] as $k=>$v) {
            if ($k === 'web_logo') {
                $path = '/static/img/web_log.'.substr($v['sys_value'], strrpos($v['sys_value'], '.')+1);
                copy('./'.$v['sys_value'], '.'.$path);
                $v['sys_value'] = $path;
            }
            $res[$k]['sys_code'] = $k;
            $res[$k]['sys_value'] = $v['sys_value'];
        }
        return $res;
    }
}