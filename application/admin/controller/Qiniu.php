<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-{2019} http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/2/24 22:15
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use tp_tools\Qiniu as QiniuTools;

class Qiniu extends Base
{
    private $Qi;

    /**
     * Qiniu constructor
     */
    public function __construct()
    {
        parent::__construct();
        //实例化qiniu 类
        $this->Qi = new QiniuTools();
    }

    /**
     * 获取七牛云上传token
     * @param $dir string /upload/之后后文件夹路径，如img/article；img/user
     * @return \think\response\Json
     */
    public function getUpToken($dir = 'img/temp')
    {
        $token = $this->Qi->upToken($dir);
        return json(['uptoken' => $token]);
    }

    /**
     * 上传文件
     */
    public function uploadFile()
    {
        $res = $this->Qi->upload();
        if ($res === false) {
            $this->error($this->Qi->getError());
        }
        $this->success('上传成功');
    }

    /**
     * 删除文件
     * @return array
     */
    public function deleteFile()
    {
        $param = $this->request->param();
        if (empty($param['table_name']) || empty($param['field_name']) || empty($param['id']) || empty($param['img_url'])) {
            $this->error("参数错误！");
        }
        $pk = Db::name($param['table_name'])->getPk();
        $count = Db::name($param['table_name'])->where($param['field_name'], $param['img_url'])->count();
        //其他地方未使用当前图片，则删除源文件
        if ($count <= 1) {
            $img_url = substr($param['img_url'], strpos($param['img_url'], '.com') + 5);
            $res = $this->Qi->delete($img_url);
            if ($res === false) {
                $this->error('删除失败');
            }
        }
        Db::name($param['table_name'])->where($pk, $param['id'])->update([$param['field_name'] => '']);
        $this->success('删除成功');
    }
}