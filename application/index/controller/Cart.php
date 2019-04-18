<?php
namespace app\index\controller;
use think\Model;


class Cart extends Common{
    public function changNumber()
    {
        $goods_id = input('goods_id/d');

        $goods_count = input('goods_count/d');

        $goods_attr_ids = input('goods_attr_ids');

        $model = model('Cart');

        $result = $model->changNumber($goods_id, $goods_count, $goods_attr_ids);
        if($result === FALSE){
            return json(['status'=>0,'msg'=>$model->getError()]);
        }
        return json(['status'=>1,'msg'=>'ok']);
    }
    public function remove()
    {
        $goods_id = input('goods_id/d');
        $goods_attr_ids = input('goods_attr_ids/d');
        $model = model('Cart');
        $result = $model->remove($goods_id,$goods_attr_ids);
        $this->success('ok','index');
    }
    public function index()
    {
        $data = model('Cart')->getCartlist();
        $this->assign('data',$data);
        $total = model('Cart')->getTotal($data);
        $this->assign('total',$total);
        return $this->fetch();
    }

    public function addCart()
    {
        $goods_id = input('goods_id/d');

        $goods_count = input('goods_count/d');

        $goods_attr_ids = input('goods_attr_id/a');

        //转换属性值的格式为逗号分隔的字符串

        $goods_attr_ids = $goods_attr_ids?implode(',',$goods_attr_ids):'';

        $cart_model = model('Cart');

        $result = $cart_model->addCart($goods_id,$goods_count,$goods_attr_ids);
        if($result === FALSE){
            $this->error($cart_model->getError());
        }
        $this->success('加入完成','index');
     }
}