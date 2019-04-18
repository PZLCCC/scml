<?php
namespace app\admin\controller;

use think\Request;

class Type extends Common
{
    //类型的添加
    public function add(Request $request)
    {
        if($request -> isGet())
        {
            return $this -> fetch();
        }
        model('Type')->addType(input());
        $this->success('ok','index');
    }

    //类型的列表
    public function index()
    {
        $data = model('Type')->listData();
        $this -> assign('data',$data);
        return $this -> fetch();
    }

    //类型的删除

    public function remove()
    {
        model('Type')->remove(input('id'));
        $this -> success('ok');
    }

    //类型的编辑

    public function edit(Request $request)
    {
        $model = model('Type');
        $type_id = input('id/d',0);
        if($request->isGet()){
            $info = $model->get($type_id);
            $this->assign('info',$info);
            return $this->fetch();
        }
        model('Type')->editType(input());
        $this->success('ok','index');
    }
    
}