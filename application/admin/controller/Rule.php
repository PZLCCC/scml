<?php
namespace app\admin\controller;
use think\Db;
use think\Request;

class Rule extends Common
{
    public function clearAdminCache()
    {
        //获取所有用户信息
        $users = model('Admin')->all;
        //每个用户更新缓存
        foreach ($users as $value){
            cache('admin_info_'.$value->id,null);
        }
    }
    public function edit(Request $request)
    {
        $model = model('Rule');
        $rule_id=input('id');

        if($request->isGet()){
            $info = $model->get($rule_id);
            $rules = $model->getRules();
            return $this->fetch('edit',['info'=>$info,'rules'=>$rules]);
        }
        $result = $model->editRule(input());
        if($result === FALSE){
            $this -> error($model->getError());
        }
        $this->success('ok');
    }
    public function index()
    {
        $rules = model('Rule')->getRules();
        $this->assign('rules',$rules);
        return $this->fetch();
    }
    public function add(Request $request)
    {
        if($request -> isGet()){
            $rules = model('Rule')->getRules();
            $this->assign('rules',$rules);
            return $this->fetch();
        }
        model('Rule')->allowField(true)->save(input());
        $this -> success('ok','index');
    }

}