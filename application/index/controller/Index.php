<?PHP
namespace app\index\controller;

class Index extends \think\Controller
{
    public function index(){ 
       return '<div align="center">你好,程序运行正常.你可进一步开发.&nbsp;&nbsp;<a href="phpinfo.php">phpinfo</a></div>';
    }
}
