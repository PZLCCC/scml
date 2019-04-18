<?php
namespace app\index\Model;
use think\Db;
use think\Model;

class Cart extends Model
{
    //购物车cookie转移到数据库
    public function cookie2db()
    {
        $user_id = get_user_id();
        if(!$user_id){
            return FALSE;
        }
        $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
        foreach ($cart_list as $key => $value){
            $tmp = explode('-',$key);
            $where = [
                'goods_id' => $tmp[0],
                'goods_attr_ids' => $tmp[1],
                'user_id'=>$user_id
            ];
            if($this->getDbQuery()->where($where)->find()){
                $this->getDbQuery()->where($where)->setField('goods_count',$value);
            }else{
                $where['goods_count']= $value;
                $this->getDbQuery()->insert($where);
            }

        }
        //cooki数据中的清空
        cookie('cart_list',null);
    }
    public function changNumber($goods_id,$goods_count,$goods_attr_ids)
    {
        $user_id = get_user_id();
        if ($user_id) {
            $where = [
                'goods_id' => $goods_id,
                'goods_attr_ids' => $goods_attr_ids,
                'user_id' => $user_id
            ];
            $this->getDbQuery()->where($where)->setField('goods_count',$goods_count);
        }else{
            $cart_list = cookie('cart_list') ? cookie('cart_list') : [];
            $key = $goods_id . '-' . $goods_attr_ids;
            $cart_list[$key]=$goods_count;
            cookie('cart_list', $cart_list);
        }
    }
    public function remove($goods_id,$goods_attr_ids)
    {
        $user_id = get_user_id();
        if($user_id){
           $where = [ 
            'goods_id'=>$goods_id,
            'goods_attr_ids'=>$goods_attr_ids,
            'user_id'=>$user_id
        ];
            $this->getDbQuery()->where($where)->delete();
        }else{
            $cart_list = cookie('cart_list')?cookie('cart_list'):[];
            $key = $goods_id.'-'.$goods_attr_ids;
            unset($cart_list[$key]);
            cookie('cart_list',$cart_list);
        }

    }
    //购物车运算总金额
    public function getTotal($data)
    {
        $sum = $money = 0;
        foreach ($data as $key => $value)
        {
            $sum += $value['goods_count'];
            $money += $value['goods_count']*$value['goods_info']['shop_price'];
        }
        return ['sum'=>$sum,'money'=>$money];
    }
    
    //获取购物车列表数据
    public function getCartList()
    {
        //获取购物车存在的内容
        $user_id = get_user_id();
        if($user_id){
            $where = ['user_id'=>$user_id];
            $cart_list = $this->getDbQuery()->where($where)->select();
        }else{
            $cart = cookie('cart_list')?cookie('cart_list'):[];
            //数据转换
            foreach ($cart as $key=>$value){
                $tmp = explode('-', $key);
                $cart_list[]=[
                    'goods_id'=>$tmp[0],
                    'goods_count'=>$value,
                    'goods_attr_ids'=>$tmp[1]
                ];
            }
        }
        //获取商品有关的内容
        foreach ($cart_list as $key => $value){
            $cart_list[$key]['goods_info'] = model('Goods')->getGoodsInfo($value['goods_id']);

            //根据属性值的id获取对应的属性信息
            $cart_list[$key]['attrs']= Db::name('goods_attr')->alias('a')->field('a.attr_value,b.attr_name')->
            join('shop_attribute b','a.attr_id=b.id')->where('a.id','in',$value['goods_attr_ids'])->select();
        }
        return $cart_list;
    }
    public function getDbQuery()
    {
        return Db::name('Cart');
    }
    //商品加入
    public function addCart($goods_id,$goods_count,$goods_attr_ids)
    {
        //检查库存
        if(!model('Goods')->checkGoodsNumber($goods_id,$goods_count)){
            $this->error = '没有库存';
            return FALSE;
        }

        //判断是否登录
        $user_id = get_user_id();
        if($user_id){
            $where = [
                'user_id' => $user_id,
                'goods_id' => $goods_id,
                'goods_attr_ids' => $goods_attr_ids
            ];
            //如果登录，存在相同数据就累加，不同就写入
            if($this->getDbQuery()->where($where)->find()){
                $this->getDbQuery()->where($where)->setInc('goods_count',$goods_count);
            }else{
                //写入数据
                $where['goods_count'] = $goods_count;
                $this->getDbQuery()-> insert($where);
            }
        }else{
            $cart_list = cookie('cart_list')?cookie('cart_list'):[];
            //判断要添加的物品在cookie是否存在
            $key = $goods_id.'-'.$goods_attr_ids; //组装下标
            if(array_key_exists($key,$cart_list)){
                //说明数据已存在，则加
                $cart_list[$key]+=$goods_count;
            }else{
                $cart_list[$key]=$goods_count;
            }
            cookie('cart_list',$cart_list,3600*24*7);
        }
    }
}