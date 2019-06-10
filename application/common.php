<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Db;
use think\facade\Request;
use think\facade\Session;

// 应用公共文件
if (!function_exists('get_child_ids')) {
    /**
     * 递归获取下级资料id集合
     * @param $id
     * @param $table_name
     * @param bool $merge
     * @return array
     */
    function get_child_ids($id, $table_name, $merge = true)
    {
        $id = explode(',', $id);
        $pk = Db::name($table_name)->getPk();//获取当前表主键
        $ids = Db::name($table_name)->whereIn('pid', $id)->column($pk);
        foreach ($ids as $k=>$v) {
            $ids = array_merge($ids, get_child_ids($v, $table_name, false));
        }
        if ($merge) $ids = array_merge($id, $ids);
        return $ids;
    }
}

if (!function_exists('get_parent_ids')) {
    /**
     * 递归获取上级资料id集合
     * @param $id
     * @param $table_name
     * @param bool $merge
     * @param array $res
     * @param array $map
     * @return array
     */
    function get_parent_ids($id, $table_name, &$map = [], $merge = true, &$res=[])
    {
        $pk = Db::name($table_name)->getPk();//获取当前表主键
        $table_fields = Db::name($table_name)->getTableFields();//获取当前表主键
        $map[] = [$pk, '=', $id];
        if (in_array('state', $table_fields)) {
            $map[] = ['state', '=', 1];//启用状态
        }
        $pid = Db::name($table_name)->where($map)->value('pid');
        if (!empty($pid)){
            $res[] = $pid;
            get_parent_ids($pid, $table_name, $map, false, $res);
        }
        krsort($res);//进行升序排序
        if ($merge) array_push($res, $id);
        return $res;
    }
}

if (!function_exists('array_column_multi')) {
    /**
     * 多维数组，获取指定列
     * @param $data
     * @param $res
     * @param string $key
     * @return array
     */
    function array_column_multi($data, $key = "id", &$res = []) {
        foreach ($data as $k=>$v) {
            if (!isset($res[$v[$key]])) {
                $res[$v[$key]] = $v[$key];
            }
            if (isset($v["child"])) {
                array_column_multi($v["child"], $key, $res);
            }
        }
        return $res;
    }
}

if (!function_exists('get_child_by_tree')) {
    /**
     * 获取子id （非递归）（tree方式）
     * @param  array/string $id
     * @param  string $table_name
     * @param  array $map
     * @param  bool $merge
     * @return array
     */
    function get_child_by_tree($id, $table_name, $map = [], $merge = true) {
        if (!is_array($id)) {
            $id = array_unique(explode(",", $id));
        }

        $list = Db::name($table_name)->where($map)->select();
        $pk = Db::name($table_name)->getPk();
        $ids = [];
        foreach ($id as $K=>$v) {
            $dept_child = \Tree::getTree($list, $pk, 'pid', 'child', $v, false);
            $ids = empty($ids) ? array_column_multi($dept_child, "id") : array_merge($ids, array_column_multi($dept_child, "id"));
        }
        if ($merge) $ids = array_merge($ids,$id);
        return array_unique($ids);
    }
}

if (!function_exists('serials_number')) {
    /**
     * 生成36进制流水号
     * @param $value string 待累加流水号,如没有传空
     * @param $len int 生成流水号部位长度
     * @param string $title 累加字段前缀
     * @return bool|string
     */
    function serials_number($value, $len, $title = "")
    {
        $max = str_repeat("Z", $len);
        $org_title = substr($value, 0, strlen($title));
        $value = substr($value, strlen($title));
        if ($value && ($value == $max || strlen($value) !== $len || $org_title !== $title)) {
            return false;
        } else {
            if (empty($value)) {
                return strtoupper($title . sprintf("%0" . $len . "d", 1));
            } else {
                return strtoupper($title . sprintf("%0" . $len . "s", base_convert(intval(base_convert($value, 36, 10)) + 1, 10, 36)));
            }
        }
    }
}


if (!function_exists('delete_file')) {
    /**
     * 删除文件资源
     * @param $url
     * @return bool
     */
    function delete_file($url)
    {
        $path = '';
        if (!empty($url)) {
            if (file_exists($path . $url)) {
                $status = unlink($path . $url);
            } else {
                $status = true;
            }
        } else {
            $status = false;
        }
        return $status;
    }
}

if (!function_exists('password_strength')) {
    /**
     * 检查密码强度是否合格，连续四位数递增或递减，返回false
     * @param $str
     * @return bool
     */
    function password_strength($str)
    {
        //密码长度小于6位数，返回false
        if (strlen($str) < 6) {
            return false;
        }

        //密码中包含字母 或 特殊字符，返回true
        if (preg_match("/[a-z]+/", $str) || preg_match("/[A-Z]+/", $str) || preg_match("/[_|\-|+|=|*|!|@|#|$|%|^|&|(|)]+/", $str)) {
            return true;
        }

        $res = false;
        $str_arr = str_split($str);//将字符串拆分为单个字符
        $i = 0;
        foreach ($str_arr as $key => $val) {
            if (isset($str_arr[$key + 1]) &&
                (!((intval($str_arr[$key + 1]) - intval($val) == 0) || (intval($val) - intval($str_arr[$key + 1]) == 0))
                    && !((intval($str_arr[$key + 1]) - intval($val) == 1) || (intval($val) - intval($str_arr[$key + 1]) == 1)))) {
                $res = true;//当前字符与后一个字符相比较，差值非0、非1，则返回true
                break;
            } else {
                $i++;
                if ($i == 3) {
                    $res = false;//连续四个字符都不通过，则返回false
                    break;
                }
            }
        }
        return $res;
    }
}


if (!function_exists('get_uuid')) {
    /**
     * 输出由 - 拼接的共36位唯一字符串 UUID
     * @return string
     */
    function get_uuid()
    {
        mt_srand((double)microtime() * 10000);//optional for php 4.2.0 and up.
        $charid = strtoupper(md5(uniqid(rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = substr($charid, 0, 8) . $hyphen
            . substr($charid, 8, 4) . $hyphen
            . substr($charid, 12, 4) . $hyphen
            . substr($charid, 16, 4) . $hyphen
            . substr($charid, 20, 12);
        return $uuid;
    }
}

/**
 * 获取全球唯一标识 uuid
 * @return string
 */
function get_uuid_oc()
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x', mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

if (!function_exists('delete_file_by_time')) {
    /**
     * 递归删除目录下，某一个时间点之前的文件
     * @param $dir string 目录路径
     * @param $time int 小时 72h=3天
     */
    function delete_file_by_time($dir, $time = 72)
    {
        //判断是否目录是否存在
        if (is_dir($dir)) {
            // 打开目录，然后读取其内容
            if ($dh = opendir($dir)) {
                while (($file = readdir($dh)) !== false) {
                    //file 为目录下其中一个文件的文件名
                    if ($file != "." && $file != "..") {
                        $fullpath = $dir . "/" . $file;
                        if (!is_dir($fullpath)) {
                            if ((time() - filemtime($fullpath)) / 3600 > $time) {
                                unlink($fullpath);
                            }
                        } else {
                            delete_file_by_time($fullpath, $time);
                        }
                    }
                }
            }
            closedir($dh);
        }
    }
}


if (!function_exists('imgTempFileMove')) {
    /**
     * 处理图片，从临时文件夹转移到 img/** 文件夹
     * @param array $img temp文件夹中的图片路径集
     * @param string $folder
     * @return array
     */
    function imgTempFileMove($img = [], $folder = '')
    {
        $request = Request::instance();
        $folder = !empty($folder) ? $folder : 'img/user/';//文件新目录
        foreach ($img as $k => $v) {
            //内容信息不为空，且确定为temp文件夹
            $v = str_replace($request->domain(), '', $v);
            $v = str_replace('//', '/', $v);
            if (!empty($v) && strpos($v, '/temp/') !== false) {
                $img[$k] = str_replace('/img/temp/', '/' . $folder, $v);

                if (file_exists('.' . $v)) {
                    if (!is_dir('.' . dirname($img[$k]))) {
                        // 创建目录
                        mkdir('.' . dirname($img[$k]), 0777, true);
                    }

                    //转移图片文件，从 img/temp 文件夹，移到 img/** 文件夹中
                    copy('.' . $v, '.' . $img[$k]);

                    //删除 img/temp 文件夹中对应的图片
                    delete_file($v);
                }
            }
        }
        return $img;
    }
}

if (!function_exists('sock_open')) {
    /**
     *  远程请求（不获取内容）函数，非阻塞
     * @param $url
     * @return bool
     */
    function sock_open($url) {
        $host = parse_url($url,PHP_URL_HOST);
        $port = parse_url($url,PHP_URL_PORT);
        $port = $port ? $port : (Request::instance()->isSsl() ? 443 : 80); //如没有使用HTTPS则使用80端口
        $scheme = parse_url($url,PHP_URL_SCHEME);
        $path = parse_url($url,PHP_URL_PATH);
        $query = parse_url($url,PHP_URL_QUERY);
        if($query) $path .= '?'.$query;
        if($scheme == 'https') { //判断是否使用HTTPS
            $host = 'ssl://'.$host; //如使用HTTPS则使用SSL协议
        }
        $fp = @fsockopen($host,$port,$error_code,$error_msg,1);

        if(!$fp) {
            $Log = new \app\common\controller\Log();
            $Log->saveErrorLog(['error_code' => $error_code,'error_msg' => $error_msg]);
            return false;
        }
        else {
            stream_set_blocking($fp,true);//开启非阻塞模式
            stream_set_timeout($fp,1);//设置超时
            $http_pact = $scheme == 'https' ? '' : 'HTTP/1.1';//HTTPS请求，不指定Http版本
            $header = "GET $path  $http_pact \r\n";
            $header.="Host: $host\r\n";
            $header.="Connection: close\r\n\r\n";//长连接关闭
            fwrite($fp, $header);
            usleep(1000); // 这一句也是关键，如果没有这延时，可能在nginx服务器上就无法执行成功
            //调试
//            while (!feof($fp)) {
//                echo fgets($fp, 128).'<br>';
//            }
            fclose($fp);
            return true;
        }
    }
}

if (!function_exists('is_weixin')) {
    /**
     * 判断是否微信环境
     * @return bool
     */
    function is_weixin()
    {
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}

if (!function_exists('salt_md5')) {

    /**
     * 系统非常规MD5加密方法，加盐
     * @param $str
     * @param string $key
     * @return string
     */
    function salt_md5($str, $key = 'dlx_admin')
    {
        return '' === $str ? '' : strtoupper(md5(sha1($str) . $key));
    }
}

/**
 * 获取登录管理员id
 * @return mixed
 */
function get_aid()
{
    $admin = session('admin_info');
    if (session('admin_info_sign') == data_auth_sign($admin)) {
        return $admin['admin_id'];
    } else {
        return false;
    }
}

/**
 * 获取登录用户id
 * @return mixed
 */
function get_uid()
{
    $user = session('user_info');
    if (session('user_info_sign') == data_auth_sign($user)) {
        return $user['user_id'];
    } else {
        return false;
    }
}

/**
 * 数据签名认证
 * @param $data
 * @return string
 */
function data_auth_sign($data)
{
    //数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data);                //排序
    $code = http_build_query($data);     //url编码并生成query字符串
    $sign = sha1($code);                 //生成签名
    return $sign;
}

if (!function_exists('http_curl')) {
    /**
     * curl 接口请求
     * @param $url
     * @param array $data
     * @param array $config 头文件等配置信息
     * @return mixed
     */
    function http_curl($url, $data = [], $config = [])
    {
        $ch = curl_init(); //初始化，创建一个curl 资源
        //默认头文件
        $header = array(
            "Accept: application/json",
            "Cache-Control: no-cache",
            "Pragma: no-cache"
        );

        if (!empty($config['header'])) {
            $header = array_merge($header, $config['header']);//与个性化 头文件合并
        }
        $user_agent = !empty($config['user_agent']) ? $config['user_agent'] : 'Mozilla/5.0 (Linux; Android 8.0.0; MHA-AL00 Build/HUAWEIMHA-AL00; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/63.0.3239.111 Mobile Safari/537.36/1.0';
        $data = (!empty($config['type']) && $config['type'] == 'json') ? json_encode($data) : http_build_query($data);

        curl_setopt($ch, CURLOPT_URL, $url); //设置url
        curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //请求时发送的header
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);//如果服务器超过该时间没有响应，脚本就会断开连接；
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);   //如果资源超过该时间没有完成返回，脚本将会断开连接。
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $ret = curl_exec($ch);//抓取url 并把它传递给浏览器
        curl_close($ch);//关闭curl资源，并且释放系统资源
        return $ret;
    }
}

if(!function_exists('base64_encode_image')){
    /**
     * 将图片生成base64数据流
     * @param $image_file
     * @return string
     */
    function base64_encode_image ($image_file) {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    }
}

if (!function_exists('base64_content_image')) {
    /**
     * base64格式编码转换为图片并保存对应文件夹
     * @param $base64_data
     * @param $path
     * @return bool|string
     */
    function base64_content_image($base64_data,$path){
        //匹配出图片的格式
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_data, $result)){
            if(!file_exists($path)){
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                mkdir($path, 0770, true);
            }
            $new_file = $path.md5(get_uuid()).".{$result[2]}";
            $base64 = str_replace($result[1], '', $base64_data);
            if (file_put_contents($new_file, base64_decode($base64))){
                return $new_file;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}

if (!function_exists('get_domain')) {
    /**
     * 获取当前环境域名
     */
    function get_domain()
    {
        return config('system.domain')[config('system.version')];
    }

}
