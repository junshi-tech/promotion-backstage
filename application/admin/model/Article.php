<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/11/2 15:45
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Article as CoreArticle;
use think\Db;
use tp_tools\Qiniu;

class Article extends CoreArticle
{
    /**
     * 获取文章类目下拉列表，带前置符号|----
     * @param array $map
     * @return string
     */
    public function getCatTableTree($map = [])
    {
        $map['state'] = 1;
        $map['language'] = session('system_config.language');
        $catList = Db::name('article_cat')->where($map)->field('cat_id as value,pid,cat_name as name')->order('sort_num asc, cat_id desc')->select();
        return \Tree::get_Table_tree($catList, 'name', 'value');
    }

    /**
     * 获取文章类目下拉列表HTML<option>
     * @param array $map
     * @param int $value 默认选中id
     * @return string
     */
    public function getCatOptionTree($map = [], $value = 0)
    {
        $map['state'] = 1;
        $map['language'] = session('system_config.language');
        $catList = Db::name('article_cat')->where($map)->field('cat_id,pid,cat_name')->order('sort_num asc, cat_id desc')->select();
        return \Tree::get_option_tree($catList, $value, 'cat_name', 'cat_id', 'pid');
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array|bool
     */
    public function handleSaveData($data = []){
        //图片不为空，开始处理图片
        if (!empty($data['img_url'])) {
            //获取旧图片
            $old = !empty($data['art_id']) ? Db::name('article')->where('art_id', $data['art_id'])->value('img_url') : '';
            if (empty($old) || $data['img_url'] != $old) {
                //更改云存储图片命名 temp -> article
                $new_name = str_replace('/img/temp/', '/img/article/', $data['img_url']);
                $Qiniu = new Qiniu();
                if ($Qiniu->rename($data['img_url'], $new_name) === false) {
                    $this->error = $Qiniu->getError();
                    return false;
                }
                $data['img_url'] = $new_name;
            }
        }


        //图片不为空，开始处理图片
//        if (!empty($data['img_url'])) {
//            $old = !empty($data['art_id']) ? Db::name('article')->where('art_id', $data['art_id'])->value('img_url') : '';
//            if (empty($old) || $data['img_url'] != $old) {
//                $data['img_url'] = current(imgTempFileMove([$data['img_url']], '/upload/img/article/'));//从临时文件夹移动图片
//                if (!empty($data['art_id'])) {
//                    $count = Db::name('article')->where('img_url', $old)->count();//编辑且换图，则删除旧图片
//                    //若其他地方没使用该旧图片，则删除
//                    if ($count == 1){
//                        delete_file($old);
//                    }
//                }
//            }
//        }
        return $data;
    }
}