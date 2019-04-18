<?php
namespace app\index\controller;
use think\Controller;

class Common extends Controller{
    public function __construct()
    {
        //执行父类的构造方法
        parent::__construct();

        $category = model('Category')->getCateInfo();
        $this->assign('category',$category);
    }

    //检查是否登录
    public function checkUserLogin()
    {
        $user_id = get_user_id();
        if(!$user_id){
            $this->error('未登录','User/login');
        }
    }
}