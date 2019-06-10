<?PHP
namespace app\index\controller;

use think\Db;

class Index extends \think\Controller
{
    public function index(){ 
        phpinfo();
    }

    public function demo(){ 
        phpinfo();
    }
}
