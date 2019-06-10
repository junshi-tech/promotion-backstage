<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2019/3/7 16:56
// +----------------------------------------------------------------------

namespace app\soldier\controller;

use tp_tool\UploadImage;

class Upload extends Base
{

    /**
     * 上传图片
     * @return array
     */
    public function image()
    {
        //测试数据
        $_REQUEST['image'] = $_POST['image'] ?? base64_encode_image('./static/img/avatar/user42.png');

        $upload = new UploadImage();
        $date = date('Ym');
        $this->result['data']['img'] = get_domain().$upload->file('image')->store('img/'.$date. '/')->compress();

        return $this->result;
    }



}