<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2018-2018 http://www.donglixia.net All rights reserved.
// +----------------------------------------------------------------------
// | Author: 十万马 <962863675@qq.com>
// +----------------------------------------------------------------------
// | DateTime: 2018/10/29 20:12
// +----------------------------------------------------------------------

namespace app\admin\model;

use app\common\model\Base;
use think\Db;

class BasicMenu extends Base
{
    /**
     * 定义数据表主键
     * @var string
     */
    protected $pk = 'menu_id';

    /**
     * 获取数据列表tree
     * @param array $map
     * @return array
     */
    public function getDataTree($map = [])
    {
        $list = Db::name('basic_menu')->field("menu_id,pid,menu_name as title,icon,layui_jump as jump,is_extend as spread")->where($map)->order("sort_num")->select();
        foreach ($list as $k=>$v) {
            $arr = explode('/', $v['jump']);
            foreach ($arr as $key=>$val) {
                if (strpos($val, '=') !== false)
                    unset($arr[$key]);
            }
            $list[$k]['name'] = end($arr);
        }
        return \Tree::getTree($list, "menu_id", "pid", "list");
    }

    /**
     * 获取数据列表TableTree
     * @param array $map
     * @return string
     */
    public function getDataTableTree($map = [])
    {
        $list = $this->field("menu_id,pid,menu_name,icon,url,layui_jump as jump,
        display as display_text, open_type as open_type_text,describe")
            ->where($map)->order("sort_num")->select()->toArray();
        return \Tree::get_Table_tree($list, "menu_name", "menu_id");
    }

    /**
     * 获取菜单列表
     * @param $value int 默认选中id
     * @param array $map
     * @return string
     */
    public function getMenuOptionTree($value = 0, $map = [])
    {
        $list = Db::name('basic_menu')->field('menu_id,pid,menu_name')->where($map)->order('sort_num')->select();
        return \Tree::get_option_tree($list, $value, 'menu_name', 'menu_id');
    }

    /**
     * 是否默认展开
     * @param $value
     * @return bool
     */
    public function getSpreadAttr($value)
    {
        return $value == 1 ? true : false;
    }

    /**
     * 获取“显示”字段，中文名称
     * @param $value
     * @return string
     */
    protected function getDisplayTextAttr($value)
    {
        return $value == '1' ? '是' : '否';
    }

    /**
     * 获取“打开方式”字段，中文名称
     * @param $value
     * @return string
     */
    protected function getOpenTypeTextAttr($value)
    {
        return $value == '1' ? '当前窗口' : '新窗口';
    }

    /**
     * 保存前处理数据
     * @param array $data
     * @return array
     */
    public function handleSaveData($data = []){
        //顺序按最大值加1
        if (empty($data['sort_num'])) {
            $count = $this->where('pid', $data['pid'])->count();
            $data['sort_num'] = $count+1;
        }
        return $data;
    }
}

