<?php
namespace app\index\controller;
use think\Db;

class Goods extends Common
{
    public function detail()
    {
        //获取商品信息
        $goods_id = input('goods_id');
        $goods = model('Goods')->getGoodsInfo($goods_id);
        $this->assign('goods', $goods);
        return $this->fetch();
    }
}