<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018-02-09 16:17
// +----------------------------------------------------------------------

namespace app\common\model;

use think\Exception;
use think\Model;
use think\Db;
use think\facade\Session;
use think\facade\Request;
use traits\controller\Jump;

abstract class Base extends Model
{
    use Jump;
    protected $autoWriteTimestamp = true;
    //save方法原始数据
    protected $saveData = [];
    protected $deleteTime = null;
    protected $createTime = false;
    protected $updateTime = false;

    public function __construct($data = [])
    {
        parent::__construct($data);

        $db = $this->db(false);

        if (in_array('create_time',$db->getTableFields())) {
            $this->createTime = 'create_time';
        }
        if (in_array('update_time',$db->getTableFields())) {
            $this->updateTime = 'update_time';
        }
        if (in_array('create_by',$db->getTableFields())) {
            array_push($this->insert,'create_by');
        }
        if (in_array('update_by', $db->getTableFields())) {
            array_push($this->insert,'update_by');
        }
        if (in_array('update_by', $db->getTableFields())) {
            array_push($this->update,'update_by');
        }
        if (in_array('language', $db->getTableFields())) {
            array_push($this->insert,'language');
        }
    }

    public function setCreateByAttr($value)
    {
        return $value ?? (get_aid() ?? 0);
    }

    public function setupdateByAttr($value)
    {
        return $value ?? (get_aid() ?? 0);
    }

    public function setLanguageAttr($value)
    {
        return $value ?? (Session::get('system_config.language') ?? 1);
    }

    /**
     * 保存当前数据对象
     * @access public
     * @param array  $data     数据
     * @param array  $map    更新条件
     * @param string $sequence 自增序列名
     * @return integer|false
     */
    public function save($data = [], $map = [], $sequence = null)
    {
        $data = $data ?? $this->getData();
        $this->saveData = $data;
        $this->allowField(true);
        $pk = $this->getPk();

        if (!empty($data[$pk])) {
            $this->isUpdate();
        } else {
            $table_name = $this->getTable();
            $pk_type = $this->getFieldsType($table_name,$pk);
            if (strpos($pk_type, 'char') !== false ) {
                $data[$pk] = get_uuid();
            }
        }

        $result = parent::save($data, $map, $sequence);
        //保存数据变更日志
        if ($result === true) {
            $this->dataChangelog($data);
        }
        return $result;
    }

    /**
     * 将新增和修改的数据写入日志
     * @param $data
     * @param $type int 类型：1insert，2update， 3删除
     * @return bool
     */
    public function dataChangelog($data = [], $type = 0)
    {
        if (!is_array($data)) {
            $data = $data->toArray();
        }
        if (count($data) == 2 && isset($data['sort_num'])) {
            return true;
        }

        //过滤非表字段数据,反转字段名为键,获取与当前save数据的交集
        $paramsJson = !empty($this->getTableFields) ? array_intersect_key($data, array_flip($this->getTableFields)) : $data;
        $logData['table_name'] = $this->name;
        $logData['type'] = $type ?? ($this->hasPk($data) ? 2 : 1);
        $logData['content'] = json_encode($paramsJson, JSON_UNESCAPED_UNICODE);
        $logData['create_by'] = Session::get('user_info.admin_id') ?? ($paramsJson['create_by'] ?? 0);
        $logData['create_time'] = time();
        Db::name('log_data_change')->insert($logData);
    }

    public function uploadImg($file,$path='')
    {
        $fileClass = Request::file($file);
        if($fileClass)
        {
            if($path)
            {
                $info = $fileClass->move('./static/img/' . $path, md5_file($fileClass->getInfo('tmp_name')));
                return '/static/img/' .$path.DS.$info->getSaveName();
            }
            else
            {
                $info = $fileClass->move('./static/img');
                return '/static/img'.$info->getSaveName();
            }
        }

        return false;
    }


    /**
     * 判断是否带有主键数据
     * @param array $data
     * @return bool
     */
    private function hasPk($data)
    {
        $find = $this->get($this->checkPkData($data));
        return empty($find) ? false : true;
    }

    /**
     * 得到主键数据
     * @param $data
     * @return array|int
     */
    private function checkPkData($data)
    {
        $pk = $this->getPk();
        if(is_array($pk) && count(array_intersect_key($data, array_flip($pk))) === count($pk))
        {
            return array_intersect_key($data, array_flip($pk));
        }
        elseif (is_string($pk))
        {
            return isset($data[$pk]) ? $data[$pk] : 0;
        }
        else
        {
            \think\Log::error($this->getTable().'未找到主键数据:'.json_encode($data));
            throw new \think\exception\HttpException(500, '请求数据异常');
        }
    }

    /**
     * 获取当前实例化后的模型对应的表名
     * @return bool|string
     */
    public function getTableName()
    {
        return $this->name;
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array
     */
    public function handleSaveData($data = []){
        return $data;
    }
}