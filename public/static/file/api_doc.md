## “我是一个军人”接口说明


[TOC] 生成目录

### 公众号授权登录
 
示例：http://www.ingcu.com/Wechat/login.html?back_url=http://www.baidu.com
*跳转，重定向的方式获取openid

|参数说明(GET)：|------|
|--------------|--------------|
|back_url： |	回调地址|


|跳转URL携带参数说明(GET)：|------|
|--------------|--------------|
|openid：    |	获取到的用户openid|


### 获取军人信息  
  
示例：http://www.ingcu.com/soldier/getData.html?id=fd1b3f80-0440-0e5d-2ef0-ebfeb3ec3058

|参数说明(GET)：|------|
|--------------|--------------|
|id |	军人主键id|
|user_id       |	授权用户id|

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

示例：http://www.ingcu.com/soldier/saveData

|参数说明（POST）:|----|
|--------------|--------------|
|user_id       |	授权用户id|
|username      |    （必填）姓名|
|phone      |   （必填）手机号|
|join_time      |   入伍日期|
|rank      |    职级|
|type      |    兵种|
|img_url      | 图片路径，数组|
|返回结果:  |	{"code":1,"msg":"success","data":{"id":"2646858B-2D92-8AC8-B5CE-72EFD46FF047"}}|


### 保存点赞支持    

示例：http://www.ingcu.com/soldier/saveLike

|参数说明（POST）:|----|
|--------------|--------------|
|user_id       |	授权用户id|
|soldier_id       |	军人主键id|
|返回结果:   |	{"code":0,"msg":"点赞信息已存在，请勿重复操作！","data":[]}|

### 排行榜数据

示例：http://www.ingcu.com/soldier/getRanking

|参数说明（GET）:|----|
|--------------|--------------|
|user_id       |	授权用户id|

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

示例：http://www.ingcu.com/soldier/getJoinNum

|参数说明（GET）:|----|
|--------------|--------------|
|user_id       |	授权用户id|
|soldier_id       |	军人主键id|
|返回结果:   |	{"code":1,"msg":"success","data":{"number":5002}}|


### 上传照片

