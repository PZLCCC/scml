<?php
namespace app\index\controller;
use think\Controller;

class Index extends Common
{
    //测试redis方法
    // public function test(){
    //     $value = \redisCache::instance()->test();
    //     dump($value);
    // }
    public function index()
    {
        $goods_model = model('Goods');

        $rec_goods = [];
        $rec_goods['hot'] = $goods_model -> getRecGoods('is_hot');
        $rec_goods['rec'] = $goods_model-> getRecGoods('is_rec');
        $rec_goods['new'] = $goods_model->getRecGoods('is_new');

        $this->assign('rec_goods',$rec_goods);
        //分配标识为首页界面
        $this->assign('is_index',1);
        return $this->fetch();
    }

}
