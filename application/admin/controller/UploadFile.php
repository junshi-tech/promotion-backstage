<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/16 11:16
// +----------------------------------------------------------------------

namespace app\admin\controller;

use think\Db;
use tp_tools\ImgCompress;

class UploadFile extends Base
{
    /**
     * @note上传图片
     * @return \think\response\Json
     */
    public function addImg()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error('上传数据为空');
        } else {
            $info = $file->move('./upload/img/temp/', time() . rand(100, 999));
            if ($info == false) {
                $this->error($file->getError());
            } else {
                $image_url = '/upload/img/temp/' . $info->getSaveName();
                return json(['code' => 1, 'msg' => '上传成功', 'data' => $image_url], 200, ['Content-Type' => 'text/html']);
            }
        }
    }

    /**
     * @note删除图片
     */
    public function deleteImg()
    {
        $param = $this->request->param();
        if (empty($param['table_name']) || empty($param['field_name']) || empty($param['id']) || empty($param['img_url'])) {
            $this->error("参数错误！");
        }
        $pk = Db::name($param['table_name'])->getPk();
        $count = Db::name($param['table_name'])->where($param['field_name'], $param['img_url'])->count();
        if ($count <= 1) {
            delete_file($param['img_url']);
        }
        Db::name($param['table_name'])->where($pk, $param['id'])->update([$param['field_name'] => '']);
        $this->success('操作成功');
    }

    /**
     * @note上传图片（编辑器）
     * @return \think\response\Json
     */
    public function addImgEditor()
    {
        $file = $this->request->file('file');
        if (empty($file)) {
            $this->error('上传数据为空');
        } else {
            $info = $file->move('./static/img/editor/', time() . rand(100, 999));
            if ($info == false) {
                $this->error($file->getError());
            } else {
                $image_name = $info->getSaveName();
                $image_url = '/static/img/editor/' . $image_name;
                return json(['code' => 1, 'msg' => '上传成功', 'data' => ['src' => $image_url, 'title' => $image_name]], 200, ['Content-Type' => 'text/html']);
            }
        }
        exit;
    }

    /**
     * 压缩图片
     * @param $source
     * @return string
     */
    public function imgCompress($source = './upload/img/temp/1542263317384.jpg')
    {
        if (!file_exists($source)) {
            return false;
        }

        $path = './upload/'.date('Ym');
        if(!file_exists($path)){
            mkdir($path,0777,true);
        }

        $image_name = md5(time().$source);
        $dst_img = $path.'/'.$image_name.'.jpg';
        $percent = 1;  #原图压缩，不缩放，但体积大大降低
        $Compress = new ImgCompress($source,$percent);
        $image = $Compress->compressImg($dst_img);
        return $dst_img;
    }
}