/**

 @Name：全局配置
 @Author：十万马
 @Site：http://www.layui.com/admin/

 */

layui.define(['laytpl', 'layer', 'element', 'util'], function (exports) {
    exports('setter', {
        container: 'LAY_app' //容器ID
        , base: layui.cache.base //记录layuiAdmin文件夹所在路径
        , views: layui.cache.base + 'views/' //视图所在目录
        , entry: 'index' //默认视图文件名
        , engine: '.html' //视图文件后缀名
        , pageTabs: true //是否开启页面选项卡功能。单页版不推荐开启

        , name: '东篱下 后台系统'
        , tableName: 'dlx' //本地存储表名
        , MOD_NAME: 'admin' //模块事件名

        , debug: false //是否开启调试模式。如开启，接口异常时会抛出异常 URL 等信息

        , interceptor: true //是否开启未登入拦截
        // 自定义设置全局变量
        , admin_set: {
            domain: '' //接口域名
            , table_limit: 15 //数据表，每页显示数量
            , table_limits : [10,15,30,50,100,200]//数据表，分页“下拉框”候选项
            , editor_config:{
                uploadImage: {
                    url: '/admin/upload_file/addImgEditor' //接口url
                    ,type: 'post' //默认post
                }
            }
            , upload_url: '/admin/upload_file/addImg'
            , upload_url_qiniu: '/admin/qiniu/uploadFile'
            , upload_size: '4096'
        }

        //自定义请求字段
        , request: {
            tokenName: 'access_token' //自动携带 token 的字段名。可设置 false 不携带。
        }

        //自定义响应字段
        , response: {
            statusName: 'code' //数据状态的字段名称
            , statusCode: {
                ok: [0,1] //数据状态一切正常的状态码
                , logout: 1001 //登录状态失效的状态码
            }
            , msgName: 'msg' //状态信息的字段名称
            , dataName: 'data' //数据详情的字段名称
        }

        //独立页面路由，可随意添加（无需写参数）
        , indPage: [
            '/alone/login' //登入页
            , '/alone/reg' //注册页
            , '/alone/forget' //找回密码
        ]

        //扩展的第三方模块
        , extend: [
            'echarts', //echarts 核心包
            'echartsTheme', //echarts 主题
            'qiNiu' //js上传七牛云存储
        ]

    });
});
