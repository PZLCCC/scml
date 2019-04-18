<?php
namespace app\admin\controller;
use think\Db;
use think\Request;

class Role extends Common
{
    public function disfetch(Request $request)
    {
        $rule_model = model('Rule');
        $role_id = input('id');
        if($request->isGet()){
            $info = Db::name('role')->find($role_id);
            $this->assign('hasRules',$info['rule_ids']);
            $rules = $rule_model->getRules();
            $this->assign('rules',$rules);
            return $this->fetch();
            
        }

        $rule = input('rule/a');

        $rule_ids = implode(',',$rule);
        //分割
        Db::name('role')->where('id',$role_id)->setField('rule_ids',$rule_ids);
        $this->success('ok', 'index');
        model('Rule')->clearAdminCache;
    }
    public function add(Request $request)
    {
        if($request -> isGet())
        {
            return $this->fetch();
        }
        $data = ['role_name'=>input('role_name')];
        Db::name('role')->insert($data);
        $this-> success('ok','index');
        
    }
    public function index()
    {
        $data = Db::name('role')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function remove()
    {
        $role_id = input('id/d');
        if($role_id<=1){
            //id表示为超级管理员时不可删除
            $this->error('参数错误');
        }
        Db::name('role')->delete($role_id);
        $this->success('ok','index');
    }
}