<?php
namespace app\admin\controller;
use think\Request;
use think\Db;

class Admin extends Common
{
    public function edit(Request $request)
    {
        $model = model('Admin');
        $admin_id = input('id');
        if($admin_id<=1){
            $this->error('参数错误');
        }
        if($request->isGet()){
            $roles = Db::name('role')->select();
            $this->assign('roles',$roles);
            $info = $model ->get($admin_id);
            $this->assign('info',$info);
            return $this->fetch();

        }
        $result = $model->editUser(input());
        if($request === false)
        {
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    public function add(Request $request)
    {
        if($request -> isGet()){
            $roles = Db::name('role')->select();
            $this -> assign('roles',$roles);
            return $this->fetch();
        }
        $model = model('Admin');
        $result = $model->addUser(input());
        if($result === FALSE){
            $this->error($model->getError());
        }
        $this->success('ok','index');
    }
    public function index()
    {
        $model = model('Admin');
        $data = $model-> alias('a')->join('shop_role b','a.role_id = b.id')->field('a.*,b.role_name')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
}