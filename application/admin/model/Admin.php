<?php
namespace app\admin\model;
use think\Model;

class Admin extends Model{
    public function editUser($data)
    {
        $where = [
            'username' => $data['username'],
            'id'=>['neq',$data['id']]
        ];
        if($this->get($where))
        {
            $this->error= '用户名重复';
            return FALSE;
        }

        if($data['password']){
            $data['password']= md5($data['password']);

        }else{
            unset($data['password']);
        }

        return $this->allowField(true)->isUpdate(true)->save($data);
    }

    public function addUser($data)
    {
		// 检查用户名的唯一
        if ($this->get(['username' => $data['username']])) {
            $this->error = '用户名重复';
            return false;
        }
		// 处理密码
        $data['password'] = md5($data['password']);
        return $this->allowField(true)->isUpdate(false)->save($data);
    }
    public function login($data)
    {
        $where = [
            'username'=>$data['username'],
            'password'=>md5($data['password'])
        ];
        $user = Admin::get($where);
        if(!$user){
            $this->error = '用户名或者密码错误';
            return FALSE;
        }
        $expire = 0;
        if(isset($data['remeber'])){
            $expire = 3600*24*3;
        }
        cookie('admin_info',$user->toArray(),$expire);

    }


}