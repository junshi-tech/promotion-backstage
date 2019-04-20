<?php
// +----------------------------------------------------------------------
// | 深圳市保联科技有限公司
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.luckyins.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 黎小龙 <shalinglom@gmail.com>
// +----------------------------------------------------------------------

namespace tp_tool;

use think\facade\Request;

/**
 * 图片上传类
 * Class UploadImage
 * @package App\Service
 */
class UploadImage
{
    private $image;
    private $imageinfo;
    private $imageName;

    /**
     * 缩放比例
     * @var float
     */
    private $percent = 1;

    /**
     * 所在一级目录
     * @var string
     */
    private $rootPath = './upload/';

    /**
     * 原图路径（包含文件名）
     * @var
     */
    private $sourceImg;

    /**
     * 保存地址, $rootPath 之后的路径部分（不包含文件名）
     * @var
     */
    private $store;

    /**
     * 图片文件,前端传过来的文件name
     * @var
     */
    private $file;

    /**
     * 设置图片文件,前端传过来的文件name
     * @param string $file
     * @return UploadImage
     */
    public function file(string $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * 设置保存地址, $rootPath 之后的路径部分
     * @param string $store
     * @return UploadImage
     */
    public function store(string $store):self
    {
        $this->store = $store;
        return $this;
    }

    /**
     * 保存原图
     * @return bool
     */
    private function save()
    {
        $request = Request::instance();
        if ($request->file($this->file)) {
            //file方式上传
            $this->sourceImg = $request->file($this->file)->store($this->store);
        } elseif ($base64 = $request->post($this->file)) {
            //base64编码式上传
            $this->sourceImg = base64_content_image($base64, $this->rootPath . $this->store);
        } else {
            return false;
        }

    }

    /**
     * 获取原图
     * @return string
     */
    public function source(): string
    {
        $this->save();
        return substr($this->sourceImg, 1);
    }

    /**
     * 设置缩放比例
     * @param float $value
     * @return UploadImage
     */
    public function setPercent(float $value): self
    {
        $this->percent = $value;
        return $this;
    }

    /**
     * 返回高清压缩图片
     * @return string
     */
    public function compress(): string
    {
        $this->save();
        $this->imageName = md5(get_uuid());
        $this->_openImage();
        $imageName = $this->_saveImage($this->imageName);

        $new_file = $this->rootPath . $this->store . '/' . $imageName;
        unlink($this->sourceImg);
        rename("./" . $imageName, $new_file);
        return substr($new_file, 1);
    }

    /**
     * 内部：打开图片
     */
    private function _openImage()
    {
        list($width, $height, $type, $attr) = getimagesize($this->sourceImg);
        $this->imageinfo = array(
            'width' => $width,
            'height' => $height,
            'type' => image_type_to_extension($type, false),
            'attr' => $attr
        );
        $fun = "imagecreatefrom" . $this->imageinfo['type'];
        $this->image = $fun($this->sourceImg);
        $this->_thumpImage();
    }

    /**
     * 内部：操作图片
     */
    private function _thumpImage()
    {
        $new_width = $this->imageinfo['width'] * $this->percent;
        $new_height = $this->imageinfo['height'] * $this->percent;
        $image_thump = imagecreatetruecolor($new_width, $new_height);
        //将原图复制带图片载体上面，并且按照一定比例压缩,极大的保持了清晰度
        imagecopyresampled($image_thump, $this->image, 0, 0, 0, 0, $new_width, $new_height, $this->imageinfo['width'], $this->imageinfo['height']);
        imagedestroy($this->image);
        $this->image = $image_thump;
    }

    /**
     * 保存图片到硬盘：
     * @param  string $dstImgName 1、可指定字符串不带后缀的名称，使用源图扩展名 。2、直接指定目标图片名带扩展名。
     * @return bool
     */
    private function _saveImage($dstImgName)
    {
        if (empty($dstImgName)) return false;
        $allowImgs = ['.jpg', '.jpeg', '.png', '.bmp', '.wbmp', '.gif'];   //如果目标图片名有后缀就用目标图片扩展名 后缀，如果没有，则用源图的扩展名
        $dstExt = strrchr($dstImgName, ".");
        $sourseExt = strrchr($this->sourceImg, ".");
        if (!empty($dstExt)) $dstExt = strtolower($dstExt);
        if (!empty($sourseExt)) $sourseExt = strtolower($sourseExt);
        //有指定目标名扩展名
        if (!empty($dstExt) && in_array($dstExt, $allowImgs)) {
            $dstName = $dstImgName;
        } elseif (!empty($sourseExt) && in_array($sourseExt, $allowImgs)) {
            $dstName = $dstImgName . $sourseExt;
        } else {
            $dstName = $dstImgName . $this->imageinfo['type'];
        }
        $funcs = "image" . $this->imageinfo['type'];
        $funcs($this->image, $dstName);
        return $dstName;
    }

    /**
     * 销毁图片
     */
    public function __destruct()
    {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}