<?php
namespace app\index\model;
use think\Model;
use think\Db;


class Goods extends Model
{
    //查询库存
    public function checkGoodsNumber($goods_id,$goods_count=1)
    {
        //获取商品的库存信息
        $goods_info = $this->getGoodsInfo($goods_id);
        //比对
        if($goods_info['goods_number'] < $goods_count)
        {
            return FALSE;
        }
        return TRUE;
    }
    public function getGoodsInfo($goods_id)
    {
        $data = Db::name('goods')->where('id',$goods_id)->find();
        if(!$data){
            return FALSE;
        }

        //获取商品的相册
        $data['imgs'] = Db::name('goods_img')->where('goods_id',$goods_id)->select();

        

        //获取商品的所有信息
        $attrs = Db::name('goods_attr')->alias('a')->field('a.*,b.attr_name,b.attr_type')->join('shop_attribute b','a.attr_id=b.id','left')
        ->where('a.goods_id',$goods_id)->select();
        foreach ($attrs as $key => $value){
            
            if($value['attr_type'] == 1){
                //唯一属性
                $data['radio'][] = $value;
            }else{
                // 单选属性 $value['attr_id'] 为属性的id
                $data['uniqid'][$value['attr_id']][] = $value;
            }
        }
        return $data;
    }

    public function getRecGoods($field)
    {

        //获取redis的操作对象
        $redisObj = \redisCache::instance();
        //组装key
        $key  = 'index_rec_goods_'.$field;

        //读取redis中的数据
        $data = $redisObj->get($key);
        if(!$data){
            $where = [
                $field=>1,
                'is_del'=>0
            ];
            $data = Db::name('Goods')->where($where)->limit(5)->select();
            //写入缓存
            $redisObj->set($key,$data);
        }
       return $data;
    }
}