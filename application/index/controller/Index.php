<?PHP
namespace app\index\controller;

use think\Db;

class Index extends \think\Controller
{
    public function index(){ 
       return '<div align="center" style="margin-top: 20px; background-color: yellow;">你好,程序运行正常.你可进一步开发.&nbsp;&nbsp;<a href="phpinfo.php">phpinfo</a></div>';
    }
    
    public function junshi(){ 
        $list = Db::table('user')->select();
        print_r($list);
        die();
    }
}
