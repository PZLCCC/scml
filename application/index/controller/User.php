<?php

namespace app\index\controller;
use think\Request;
use think\Db;


class User extends Common
{
    //邮箱激活
    public function active()
    {
        $key = input('key');
        //根据参数获取到用户的标识
        $user_id = \redisCache::instance()->get($key);
        if(!$user_id){
            $this->error('连接地址失效');
        }
        //完成激活操作
        Db::name('user')->where('id',$user_id)->setField('status','1');
        \redisCache::instance()->delete($key);

        $this->success('ok','login');

    }
    //邮箱注册
    public function registByEmail(Request $request)
    {
        if($request->isGet())
        {
            return $this->fetch();
        }
        $model = model('User');
        $result = $model->regist(input(),'email');
        if($result === FALSE){
            $this->error($model->getError());
        }
        $this->success('完成注册','login');
    }

    //给指定手机号发送短信验证码
    public function sendSms()
    {
        $tel = input('tel');

        if(!$tel){
            return json(['status'=>0,'msg'=>'tel fail']);
        }
        //计算验证码的内容
        $code = rand(1000,9999);
        //发送短信验证
        $res = send_sms($tel,[$code,'10']);

        //一旦验证码发送就保存验证码的数据
        if($res){
            $data = [
                'code'=>$code,
                'time'=>time()
            ];
            session('user_regist_code',$data);
            return json(['status'=>1,'msg'=>'ok']);
        }
        return json(['status'=>0,'msg'=>'网络异常']);
    }
    public function logout()
    {
        session('user_info',null);
        $this->success('完成退出','login');
    }
    public function makeCaptcha()
    {
        $config = [
            'length' => 4,
            'codeSet' => '123456789'
        ];
        $obj = new \think\captcha\Captcha($config);
        return $obj->entry();
    }
    public function regist(Request $request)
    {
        if($request->isGet()){
            return $this->fetch();
        }
        $data = input();
        $obj = new \think\captcha\Captcha();
        if (!$obj->check($data['makeCaptcha'])) {
            $this->error('验证码错误');
        }
        $model = model('User');
        $result = $model->regist($data);
        if($request === FALSE)
        {
            $this->error($model->getError());
        }
        $this->success('完成注册','login');
    }
    public function login(Request $request)
    {
        if ($request->isGet()) {
            return $this->fetch();
        }
        $data = input();
        $obj = new \think\captcha\Captcha();
        if (!$obj->check($data['makeCaptcha'])) {
            $this->error('验证码错误');
        }
        $model = model('User');
        $result = $model->login(input('username'), input('password'));
        if ($request === false) {
            $this->error($model->getError());
        }
        $this->redirect('index/index');
    }
}