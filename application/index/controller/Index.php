<?PHP
namespace app\index\controller;

use think\Db;

class Index extends \think\Controller
{
    public function index(){ 
        $list = Db::table('user')->select();
        print_r($list);
        die();
    }
}
