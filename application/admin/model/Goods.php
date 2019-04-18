<?php

namespace app\admin\model;
use think\Model;
use think\Db;

class Goods extends Model
{
    public function getRecGoods($field)
    {

        //获取redis的操作对象
        $redisObj = \redisCache::instance();
        //组装key
        $key = 'index_rec_goods_' . $field;

        //读取redis中的数据
        $data = $redisObj->get($key);
        if (!$data) {
            $where = [
                $field => 1,
                'is_del' => 0
            ];
            $data = Db::name('Goods')->where($where)->limit(5)->select();
            //写入缓存
            $redisObj->set($key, $data);
        }
        return $data;
    }
    public function changeStatus($goods_id,$field)
    {
        $goods_info = $this->where('id',$goods_id)->find();
        $status = $goods_info->getAttr($field);

        $status = $status ? 0: 1;

        $this -> where('id',$goods_id)->setField($field,$status);
        return $status;
    }
    public function deleteGoods($goods_id)
    {
        return $this->where('id',$goods_id)->delete();
    }
    public function editGoods($data)
    {
        //处理推荐转态
        $data['is_hot'] = isset($data['is_hot']) ? 1 : 0;
        $data['is_new'] = isset($data['is_new']) ? 1 : 0;
        $data['is_rec'] = isset($data['is_rec']) ? 1 : 0;
        return $this -> allowField(true) -> isUpdate(TRUE) -> save($data);
    }
    public function getGoodsInfo($goods_id)
    {
        return Goods::get($goods_id);
    }
    public function listData($is_del=0)
    {
        $where = [
            'is_del' => $is_del,
        ];
        $cate_id = input('cate_id');
        if($cate_id){
            $child = model('Category')->getTree($cate_id,true);
            foreach ($child as $key => $value) {
                $cate_ids[] = $value -> id();
            }
            $cate_ids[] = $cate_id;
            $where['cate_id'] = ['in',$cate_ids];
                }
        $keyword = input('keyword');
        if($keyword){
            $where['goods_name']=['like','%'.$keyword.'%'];
        }

        //使用推荐态度搜索

        $intro_type = input('intro_type');
        if($intro_type){
            $where[$intro_type] = 1;
        }
        $list =  $this->where($where)->paginate(2,false,['query'=>input()]);
        return $list;

    }
    public function addGoods($data)
    {
		// 追加添加时间
        $data['addtime'] = time();
        Db::startTrans();//开启事物
        try {
			// allowField方法会根据数据表的字段对非数据表字段的内容的数据过滤
            $this->allowField(true)->save($data);
			// 商品属性值信息的入库
			// 1、获取商品的id
            $goods_id = $this->getLastInsID();
			// 2、获取属性id 
            $attr_ids = input('attr_ids/a');
			// 3、获取属性值信息
            $attr_values = input('attr_values/a');
			// 调用模型方法实现数据格式化并且写入
            model('GoodsAttr')->insertAttr($goods_id, $attr_ids, $attr_values);
			// 商品的相册的入库
            model('GoodsImg')->insertImages($goods_id);
            Db::commit();
        } catch (\Exception $e) {
            $this->error = '写入数据错误';
            Db::rollback();
            return false;
        }
    }
}