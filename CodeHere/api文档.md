# API文档  
## 新建用户
***
- **投递方式**：POST  
- **路径**：/email/users
- **参数**：{
    "email": "value1", 
    "password": "value2", 
}

- **回复**：


    - 用户已存在: {
    "state":402,
    "msg":"email has exist"
    }

    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 邮件验证码发送成功: {
    "state": 200, 
    "msg":"success"
    }

***

- **投递方式**：POST  
- **路径**：/phone/users
- **参数**：{
    "phone": "value1", 
    "password": "value2", 
    "code":"value3",
}

- **回复**：- 


    - 用户已存在: {
    "content": null, 
    "state":402,
    "msg":"phone has exist"
    }

    - 验证码错误或缺失:{
    "state":404，
    "msg":"wrong code"
    }

    - 参数缺失:{
    "state":404,
    "msg":"message required"
    }


    - 用户创建成功: {
    "content":emailUser,
    "state": 200, 
    "msg":"success"
    }
***
- **投递方式**：GET  
- **路径**：email/users/active/{email}/{emailActiveToken}
- **参数**：{
-   "email":"value1",
    "emailActiveToken":"value2"
}

- **回复**：


    - 用户不存在: {
    "content": null, 
    "state":404,
    "msg":"email not found"
    }

    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 用户激活成功: {
    "state": 200, 
    "msg":"success"
    }
***
- **投递方式**：GET  
- **路径**：phone/users/code
- **参数**：{
    "phone":"vaule1"
}

- **回复**：


    - 短信发送过于频繁: {
    "state":402,
    "msg":"send too frequently"
    }
    
    - 参数缺失:{
    "state":404,
    "msg":"message required"
    }
    
    - 短信发送此成功: {
    "state": 200, 
    "msg":"success"
    }
***
## 用户登录
***
- **投递方式**：GET  
- **路径**：/email/token
- **参数**：{
    "email": "value1", 
    "password": "value2", 
}

- **回复**：


    - 用户不存在: {
    "state":404,
    "msg": "email not exists"
    }
    
    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 密码错误:{
    "state":404,
    "msg":"wrong password"
    }

    - 用户登录成功: {
    - cookie:token
    "state": 200, 
    "msg":"success"
    }
    
    
***
- **投递方式**：GET  
- **路径**：/phone/token
- **参数**：{
    "phone": "value1", 
    "password": "value2", 
}

- **回复**：


    - 用户不存在: {
    "state":404,
    "msg": "phone not exists"
    }
    
    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 密码错误:{
    "state":404,
    "msg":"wrong password"
    }

    - 用户登录成功: {
    - cookie:token
    "state": 200, 
    "msg":"success"
    }
***
## 用户登出
***
- **投递方式**：DELETE  
- **路径**：/phone/token
- **参数**：{
    "phone": "value1", 
}

- **回复**：


    - 用户不存在: {
    "state":404,
    "msg": "phone not exists"
    }
    
    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 用户登出成功: {
    "state": 200, 
    "msg":"success"
    }
    
***
- **投递方式**：DELETE  
- **路径**：/email/token
- **参数**：{
    "email": "value1", 
}

- **回复**：
    

    - 用户不存在: {
    "state":404,
    "msg": "email not exists"
    }
    
    - 参数缺失:{
    "state":400,
    "msg":"message required"
    }

    - 用户登出成功: {
    "state": 200, 
    "msg":"success"
    }