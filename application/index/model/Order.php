<?php
namespace app\index\model;
use think\Db;
use think\Model;

class Order extends Model{

    
    public function order()
    {
        $cart_model = model('Cart');

        $cart_list = $cart_model->getCartList();

        $total = $cart_model->getTotal($cart_list);

        $order_info = input();

        $order_info['user_id']=get_user_id();
        $order_info['order_sn']=date('Ymdhis').rand(10000,99999);
        $order_info['addtime'] = time();
        $order_info['total_money']=$total['money'];

        db('order')->insert($order_info);

        $order_info['id'] = db('order')->getLastInsId();

        $order_detail = [];

        foreach ($cart_list as $key => $value){
            $order_detail[]=[
                'order_id'=>$order_info['id'],
                'goods_id'=>$value['goods_id'],
                'goods_count'=>$value['goods_count'],
                'goods_attr_ids'=>$value['goods_attr_ids']
            ];
        }
        db('goods_detail')->insertAll($order_detail);

        return $order_info;

    }
}