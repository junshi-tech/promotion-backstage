<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\facade\Route;

/**
 * 我是一个军人
 */
//默认主页
Route::any('/', function () {
    return 'hello,world!';
});

//公众号授权登录
Route::any('soldier/Wechat/Login', 'soldier/WeChat/oauth');
//微信登录回调地址
Route::any('soldier/Wechat/OauthCallback', 'soldier/WeChat/oauthCallback');
//微信服务器响应
Route::any('soldier/Wechat/Server', 'soldier/WeChat/server');
//获取测试token
Route::any('soldier/Wechat/getTestToken', 'soldier/WeChat/getTestToken');
//获取军人信息
Route::get('soldier/getData', 'soldier/PicSoldier/getData');
//获取参与人数
Route::get('soldier/getJoinNum', 'soldier/PicSoldier/getJoinNum');

/*需要登录鉴权的接口*/
Route::group('soldier', function () {
    //获取授权用户信息
    Route::get('Wechat/getUserInfo', 'soldier/WeChat/getUserInfo');

    //保存军人信息
    Route::post('saveData', 'soldier/PicSoldier/save');

    //上传照片
    Route::post('uploadImg', 'soldier/Upload/image');

    //保存点赞支持
    Route::post('saveLike', 'soldier/PicSoldier/saveLike');

    //排行榜数据
    Route::get('getRanking', 'soldier/PicSoldier/getRanking');

})->middleware(app\http\middleware\CheckToken::class);