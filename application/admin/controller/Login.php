<?php
    namespace app\admin\controller;
    use think\Request;
    use think\Controller;
    use think\Db;

    class Login extends Controller
    {
        public function makePass()
        {
            return md5(input('pass'));
        }
        public function logout()
        {
            cookie('admin_info', null);
            $this->success('退出登陆', 'index');
        }
        public function captcha()
        {
            $config = [
                'length'=>4,
                'codeSet'=>'123456789'
            ];
            $obj = new \think\captcha\Captcha($config);
            return $obj->entry();
        }
        //完成用户的登录操作
        public function index(Request $request)
        {
            if($request->isGet()){
                return $this->fetch();
            }
            $data = input();
            $obj = new \think\captcha\Captcha();
            if(!$obj->check($data['captcha'])){
                $this->error('验证码错误');
            }
            $model =model('Admin');
            $result = $model->login($data);
            if($result === false){
                $this->error($model->getError());
            }
            $this-> success('完成登录','index/index');
        }
    }