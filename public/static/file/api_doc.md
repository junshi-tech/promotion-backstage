## “我是一个军人”接口说明


[TOC] 生成目录

### 公众号授权登录
 
示例：http://junshi.ingcu.com/soldier/Wechat/Login.html?back_url=http://m.baidu.com

*前端使用跳转方式，后台调用微信并重定跳回到当前参数 back_url 地址，获取token

|参数说明(GET)：|------|
|--------------|--------------|
|back_url |	成功获取token后的回调地址，如：http://www.baidu.com|

成功结果：https://m.baidu.com/?token=oPthy55wIachJ-C9YkCmKHPy8gt4

### 获取测试token
http://junshi.ingcu.com/soldier/Wechat/getTestToken?userId=002aafd5-971a-2820-9430-2ad28b02f690
http://junshi.ingcu.com/soldier/Wechat/getTestToken?userId=1811b7c2-85a2-9776-bc92-7b4f94275bc0

### 获取授权用户信息（头像、soldier_id等）  
  
示例：http://junshi.ingcu.com/soldier/Wechat/getUserInfo?token=21937e23a6fef73336109ef32cc81df9

|参数说明(GET)：|------|
|--------------|--------------|
|token       |	用于判断是否登录|

返回结果：

    "data": {
        "user_id": "C4661F2B-0CDF-8C9D-953B-AC2D3D8B2A62",
        "nickname": "阿章",
        "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/X9SiblNYpFPNOhYquGvWPRDo2H3meNxGjEFOKMCAvozIhSDOTlguTfibIVQaA5ibG4Qn9rU84QeFjlAKzAh15icRRw/132",
        "soldier_id": "FF76E64B-8F84-EB65-963F-0CDFD91033D5"
    }

### 获取军人信息  
  
示例：http://junshi.ingcu.com/soldier/getData.html?soldier_id=fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3058&token=oPthy55wIachJ-C9YkCmKHPy8gt4

|参数说明(GET)：|------|
|--------------|--------------|
|soldier_id       |	军人主键id|

返回结果：

    "data": {
        "own": {
            "id": "FF76E64B-8F84-EB65-963F-0CDFD91033D5",
            "username": "智多星",
            "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/X9SiblNYpFPNOhYquGvWPRDo2H3meNxGjEFOKMCAvozIhSDOTlguTfibIVQaA5ibG4Qn9rU84QeFjlAKzAh15icRRw/132",
            "type_text": "中国人民解放军陆军",
            "rank_text": "军官",
            "join_date": "1998"
        },
        "list": [
            {
                "user_id": "C2B8E100-DF6E-8C34-B4AC-C0247F865BC9",
                "remark": "祖国因你而强大！",
                "create_time": "2019-06-04 15:54:37",
                "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLAeExOiaEkke4Xibw2Ztia31gp1bX0IMR7YnVRRdXGj1TVUXyDw08SX2E2gGibuEqIGZHwDQF3icslYcQ/132",
                "nickname": "无用"
            }
        ]
    }


### 新增/更新军人信息  

示例：http://junshi.ingcu.com/soldier/saveData

|参数说明（POST）:|----|
|--------------|--------------|
|token       |	用于判断是否登录|
|username      |    （必填）姓名|
|phone      |   （必填）手机号|
|join_time      |   入伍日期|
|rank      |    职级|
|type      |    兵种|
|img_url[]      | 图片路径，数组|
|返回结果:  |	{"code":1,"msg":"success","data":{"id":"2646858B-2D92-8AC8-B5CE-72EFD46FF047"}}|


### 保存点赞支持    

示例：http://junshi.ingcu.com/soldier/saveLike

|参数说明（POST）:|----|
|--------------|--------------|
|token       |	（必填）用于判断是否登录|
|soldier_id       |	（必填）军人主键id|
|remark       |	（选填）备注|
|返回结果:   |	{"code":0,"msg":"点赞信息已存在，请勿重复操作！","data":[]}|

### 排行榜数据

示例：http://junshi.ingcu.com/soldier/getRanking?token=oPthy55wIachJ-C9YkCmKHPy8gt4

|参数说明（GET）:|----|
|--------------|--------------|
|token       |	用于判断是否登录|

返回结果: 

    {
        "soldier_id": "D87F9DD9-3CC9-DACD-8103-C575131B4488",
        "number": 1, 
        "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTLAeExOiaEkke4Xibw2Ztia31gp1bX0IMR7YnVRRdXGj1TVUXyDw08SX2E2gGibuEqIGZHwDQF3icslYcQ/132",
        "username": "李鬼",
        "type": "陆军"
    },
    {
        "soldier_id": "FF76E64B-8F84-EB65-963F-0CDFD91033D5",
        "number": 1,   
        "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/X9SiblNYpFPNOhYquGvWPRDo2H3meNxGjEFOKMCAvozIhSDOTlguTfibIVQaA5ibG4Qn9rU84QeFjlAKzAh15icRRw/132",
        "username": "黑旋风",
        "type": "陆军"
    }

### 获取参与人数

示例：http://junshi.ingcu.com/soldier/getJoinNum

|参数说明（GET）:|----|
|--------------|--------------|
|token       |	用于判断是否登录|
|soldier_id       |	军人主键id|
|返回结果:   |	{"code":1,"msg":"success","data":{"number":5002}}|


### 上传照片

示例：http://junshi.ingcu.com/soldier/uploadImg

|参数说明（POST）:|----|
|--------------|--------------|
|token       |	用于判断是否登录|
|image       |	图片base64数据流|
|返回结果:   |	{"code":1,"msg":"success","data":{"img":"http:\/\/ts-www.junshi.cm\/upload\/img\/20190420\/\/82057076fec1f88b5f28aaaa2b38f8d4.png"}}|
