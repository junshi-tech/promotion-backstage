<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/27 17:06
// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\common\controller\Base as CoreBase;
use app\common\model\LogApi;
use think\Db;
use think\Exception;
use think\facade\Session;
use app\common\model\Admin as AdminModel;
use app\common\controller\Log;

abstract class Base extends CoreBase
{

    /**
     * 接收参数
     * @var
     */
    protected $data;

    /**
     * 返回数据
     * @var array
     */
    protected $result = [
        'code' => 1, //成功1，失败0
        'count' => 0, //数据条数
        'msg' => 'success',
        'data' => []
    ];

    /**
     * 当前页码
     * @var
     */
    protected $page;

    /**
     * 每页数量
     * @var
     */
    protected $limit;

    /**
     * Base constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->data = $this->request->param();
        $this->page = isset($this->data["page"]) ? $this->data["page"] : 1;
        $this->limit = isset($this->data["limit"]) ? ($this->data["limit"]<10000 ? $this->data["limit"] : 10000) : 15;

        //记录访客日志
//        sock_open($this->request->domain().'/admin/log/saveVisitLog?url='.$this->request->url(true).'&user_id='.get_aid());
        (new \app\admin\controller\Log())->saveVisitLog();

        if (empty(get_aid())) {
            if (!empty($this->data['access_token'])) {
                $Admin = new AdminModel();
                $user_info = $Admin->getUserInfo(['token'=>$this->data['access_token']]);
                if ($user_info === false) {
                    $this->result['code'] = 1001;
                    $this->result['msg'] = $Admin->getError();
                    return $this->result;
                } elseif ($user_info['token_expired'] < time()) {
                    $this->result['code'] = 1001;
                    $this->result['msg'] = '登录已失效，请重新登录';
                    return $this->result;
                } else {
                    $Admin->setSession($user_info);//设置session缓存
                }
            } else {
                $this->result['code'] = 1001;
                $this->result['msg'] = '请先登陆再访问';
                return $this->result;
            }
        }
    }

    /**
     * 删除，并记录日志
     * @param $id
     */
    public function delete($id)
    {
        Db::startTrans();
        try{
            //若存在pid字段，则先删除子部门资料
            $table_fields = $this->currentModel->getTableFields();
            if (in_array('pid', $table_fields)) {
                $data_child = $this->currentModel->whereIn('pid', $id)->select();
                if (!empty($data_child)) {
                    foreach ($data_child as $k => $v) {
                        $this->currentModel->dataChangelog($v->getData(), 3);//记录删除日志
                    }
                    $this->currentModel->whereIn('pid', $id)->delete();
                }
            }

            //删除当前资料，并记录删除日志
            $pk = $this->currentModel->getPk();
            $data = $this->currentModel->whereIn($pk, $id)->select();
            if (empty($data)) {
                throw new \Exception('信息不存在');
            }
            foreach ($data as $k => $v) {
                $this->currentModel->dataChangelog($v->getData(), 3);//记录删除日志
            }
            $this->currentModel->whereIn($pk, $id)->delete();//删除当前资料
        } catch (\Exception $e) {
            Db::rollback();
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            $this->error($msg);
        }
        Db::commit();
        $this->success('删除成功!');
    }

    public function edit()
    {
        $this->result['msg'] = '调用成功';
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
            $data = $this->currentModel->handleSaveData($this->data);
            if ($data === false) {
                throw new Exception('数据处理失败！');
            }

            //保存数据
            $this->currentModel->save($data);
        } catch (\Exception $e) {
            $msg = !empty($this->currentModel->getError()) ? $this->currentModel->getError() : $e->getMessage();
            (new Log())->saveErrorLog($msg.' ['.$e->getFile().':'.$e->getLine().']');
            $this->error($msg);
        }
        $pk = $this->currentModel->getPk();
        $this->success('保存成功！', null, ['id'=>$this->currentModel->getData($pk)]);
    }

    /**
     * 更改排序
     */
    public function changeSort()
    {
        $param = $this->request->param();
        if (empty($param['id']) || empty($param['type'])) {
            $this->error('参数错误');
        }

        try {
            $list = $this->reset_sort($param['id'], $param['type']);//格式化，获取重新排序的数据
            $this->currentModel->saveAll($list);//保存数据
        } catch (\Exception $e) {
            $mag = $this->currentModel->getError() ?? $e->getMessage();
            (new Log())->saveErrorLog($mag);
            $this->error($mag);
        }

        $this->success('操作成功');
    }

    /**
     * 重新排序
     * @param $id
     * @param string $type
     * @param string $field_sort
     * @return array
     */
    private function reset_sort($id, $type = 'asc', $field_sort = 'sort_num')
    {
        //获取当前表字段名
        $pk = $this->currentModel->getPk();
        $table_fields = $this->currentModel->getTableFields();

        //判断是否存在'pid'，若存在，则只取同级别数据
        $map = [];
        if (in_array('pid', $table_fields)) {
            $map['pid'] = $this->currentModel->where($pk, $id)->value('pid');
        } elseif (in_array('cat_id', $table_fields)) {
            $map['cat_id'] = $this->currentModel->where($pk, $id)->value('cat_id');
        } elseif (in_array('language', $table_fields)) {
            $map['language'] = session('system_config.language');
        }

        $data = $this->currentModel->where($map)->field($pk . ',' . $field_sort)->order($field_sort . ' asc,' . $pk . ' desc')->select()->toArray();

        //将序号重新按1开始排序
        foreach ($data as $key => $val) {
            $data[$key][$field_sort] = $key + 1;
        }
        //处理更改排序操作
        foreach ($data as $key => $val) {
            if ($type == 'asc') {
                if (($key == '0') && $val[$pk] == $id) {
                    break;//首位菜单 点升序，直接中断
                }
                //升序操作：当前菜单序号减一，前一位的序号加一
                if ($val[$pk] == $id) {
                    $data[$key - 1][$field_sort]++;
                    $data[$key][$field_sort]--;
                    break;
                }
            } elseif ($type == 'desc') {
                if (($key == count($data)) && $val[$pk] == $id) {
                    break;//末位菜单 点降序，直接中断
                }
                //降序操作：当前菜单序号加一，后一位的序号减一
                if ($val[$pk] == $id && isset($data[$key + 1])) {
                    $data[$key][$field_sort]++;
                    $data[$key + 1][$field_sort]--;
                    break;
                }
            }
        }
        return !empty($data) ? $data : [];
    }



}