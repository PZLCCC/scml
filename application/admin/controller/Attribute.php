<?php
namespace app\admin\controller;
use think\Request;

class Attribute extends Common{
    public function edit(Request $request)
    {
       
        
        
    }
    public function remove()
    {
        model('Attribute')->remove(input('id'));
        $this->success('ok');
    }
    public function index()
    {
        $data = model('Attribute')->listData();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function add(Request $request)
    {
        if($request->isGet())
        {
            $type= model('Type')->listData();
            $this->assign('type',$type);
            return $this->fetch();
        }
        $result = model('Attribute')->addAttr(input());
        if($result === FALSE)
        {
            $this->error(model('Attribute')->getError());
        }
        $this->success('ok','index');
    }
}