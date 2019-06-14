<?php
/**
 * 配置文件
 *
 * @author 耐小心<i@naixiaoxin.com>
 * @copyright 2017-2018 耐小心
 */

return [
    /*
      * 默认配置，将会合并到各模块中
      */
    'default' => [
        /*
         * 指定 API 调用返回结果的类型：array(default)/object/raw/自定义类名
         */
        'response_type' => 'array',
        /*
         * 使用 ThinkPHP 的缓存系统
         */
        'use_tp_cache' => true,
        /*
         * 日志配置
         *
         * level: 日志级别，可选为：
         *                 debug/info/notice/warning/error/critical/alert/emergency
         * file：日志文件位置(绝对路径!!!)，要求可写权限
         */
        'log' => [
            'level' => env('WECHAT_LOG_LEVEL', 'debug'),
            'file' => env('WECHAT_LOG_FILE', app()->getRuntimePath() . "log/wechat.log"),
        ],
    ],

    //公众号
    'official_account' => [
        'default' => [
            // 公众号  绑定支付的APPID（必须配置，开户邮件中可查看）
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'wxe44a1668683a741e'),
            // AppSecret
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', '2bf666ccb585d883f84b00cacdc40a59'),
            // Token
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'KSJUNSTARSHIKONG'),
            // EncodingAESKey 加解密密钥
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', 'AddL2lBeXlYOOnVuV96Z5d5LMU7WzmtRTeSpV2gC9fi'),
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            'oauth' => [
                'scopes' => ['snsapi_userinfo'],
                'callback' => 'api/Wechat/oauthCallback',
            ],
        ],
    ],

    //公众号（测试）
    'official_account_test' => [
        'default' => [
            // 公众号  绑定支付的APPID（必须配置，开户邮件中可查看）
            'app_id' => env('WECHAT_OFFICIAL_ACCOUNT_APPID', 'wxc63e8455ef75ddaa'),
            // AppSecret
            'secret' => env('WECHAT_OFFICIAL_ACCOUNT_SECRET', '9a012b85168da8c40e05cbd8797175fc'),
            // Token
            'token' => env('WECHAT_OFFICIAL_ACCOUNT_TOKEN', 'BLWeChat1050'),
            // EncodingAESKey 加解密密钥
            'aes_key' => env('WECHAT_OFFICIAL_ACCOUNT_AES_KEY', ''),
            /*
             * OAuth 配置
             *
             * scopes：公众平台（snsapi_userinfo / snsapi_base），开放平台：snsapi_login
             * callback：OAuth授权完成后的回调页地址(如果使用中间件，则随便填写。。。)
             */
            'oauth' => [
                'scopes' => ['snsapi_base'],
                'callback' => 'api/Wechat/oauthCallback',
            ],
        ],
    ],

    //第三方开发平台
//    'open_platform'    => [
//        'default' => [
//            'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', ''),
//            'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', ''),
//            'token'   => env('WECHAT_OPEN_PLATFORM_TOKEN', ''),
//            'aes_key' => env('WECHAT_OPEN_PLATFORM_AES_KEY', ''),
//        ],
//    ],

    //开放平台，网站应用
    'web_app'    => [
        'default' => [
            'app_id'  => env('WECHAT_OPEN_PLATFORM_APPID', 'wx7eb2e3ff2035e1c4'),
            'secret'  => env('WECHAT_OPEN_PLATFORM_SECRET', 'cab23e7418edbcdbeeb218fb97f90a87'),
        ],
    ],

    //小程序
    //'mini_program'     => [
    //    'default' => [
    //        'app_id'  => env('WECHAT_MINI_PROGRAM_APPID', ''),
    //        'secret'  => env('WECHAT_MINI_PROGRAM_SECRET', ''),
    //        'token'   => env('WECHAT_MINI_PROGRAM_TOKEN', ''),
    //        'aes_key' => env('WECHAT_MINI_PROGRAM_AES_KEY', ''),
    //    ],
    //],

    //支付
    //'payment'          => [
    //    'default' => [
    //        'sandbox'    => env('WECHAT_PAYMENT_SANDBOX', false),
    //        'app_id'     => env('WECHAT_PAYMENT_APPID', ''),
    //        'mch_id'     => env('WECHAT_PAYMENT_MCH_ID', 'your-mch-id'),
    //        'key'        => env('WECHAT_PAYMENT_KEY', 'key-for-signature'),
    //        'cert_path'  => env('WECHAT_PAYMENT_CERT_PATH', 'path/to/cert/apiclient_cert.pem'),    // XXX: 绝对路径！！！！
    //        'key_path'   => env('WECHAT_PAYMENT_KEY_PATH', 'path/to/cert/apiclient_key.pem'),      // XXX: 绝对路径！！！！
    //        'notify_url' => 'http://example.com/payments/wechat-notify',                           // 默认支付结果通知地址
    //    ],
    //    // ...
    //],

    //企业微信
    //'work'             => [
    //    'default' => [
    //        'corp_id'  => 'xxxxxxxxxxxxxxxxx',
    //        'agent_id' => 100020,
    //        'secret'   => env('WECHAT_WORK_AGENT_CONTACTS_SECRET', ''),
    //        //...
    //    ],
    //],
];
