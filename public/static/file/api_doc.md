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

### 获取授权用户信息（头像等）  
  
示例：http://junshi.ingcu.com/soldier/Wechat/getUserInfo?token=21937e23a6fef73336109ef32cc81df9

|参数说明(GET)：|------|
|--------------|--------------|
|token       |	用于判断是否登录|

返回结果：

    {
        "code": 1,
        "msg": "success",
        "data": {
            "user_id": "fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3066",
            "headimgurl": "http://thirdwx.qlogo.cn/mmopen/vi_32/nQKrWbWX0V5XM06UWoDQ0aibKbdWYANChUYATW7fbwL5TiaTqqSbdWz7xOJFX62yaRjUyj0gYv464Fk7ZcDd36TQ/132"
        }
    }

### 获取军人信息  
  
示例：http://junshi.ingcu.com/soldier/getData.html?id=fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3058&token=oPthy55wIachJ-C9YkCmKHPy8gt4

|参数说明(GET)：|------|
|--------------|--------------|
|id |	军人主键id|
|token       |	用于判断是否登录|

返回结果：

    {
        "code": 1,
        "msg": "success",
        "data": {
            "id": "fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3058",
            "user_id": "fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3066",
            "username": "",
            "phone": "",
            "join_time": "1970-01-01",
            "rank": 1,
            "type": 1,
            "img_url": ""
        }
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
|token       |	用于判断是否登录|
|soldier_id       |	军人主键id|
|返回结果:   |	{"code":0,"msg":"点赞信息已存在，请勿重复操作！","data":[]}|

### 排行榜数据

示例：http://junshi.ingcu.com/soldier/getRanking?token=oPthy55wIachJ-C9YkCmKHPy8gt4

|参数说明（GET）:|----|
|--------------|--------------|
|token       |	用于判断是否登录|

返回结果: 

    {
        "code": 1,
        "msg": "success",
        "data": [
            {
                "soldier_id": "CF53E473-A520-8CBD-6F06-53D3943F0A66",
                "number": 3,
                "username": ""
            },
            {
                "soldier_id": "2646858B-2D92-8AC8-B5CE-72EFD46FF047",
                "number": 4,
                "username": "韦育章"
            }
        ]
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


