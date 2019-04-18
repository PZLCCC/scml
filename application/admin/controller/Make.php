<?php
namespace app\admin\controller;
use think\Request;
use think\Db;
use think\Model;


class Make extends Common{
    public function makeHome()
    {
        $category = model('Category')->getCateInfo();
        $this->assign('category',$category);
        $goods_model = model('Goods');
        $rec_goods = [];
        $rec_goods['hot'] = $goods_model->getRecGoods('is_hot');
        $rec_goods['rec'] = $goods_model->getRecGoods('is_rec');
        $rec_goods['new'] = $goods_model->getRecGoods('is_new');
        $this->assign('rec_goods', $rec_goods);
        //分配标识为首页界面
        $this->assign('is_index', 1);
        $content = $this->fetch('index@index/index');
        file_put_contents('index.html',$content);
    }
}