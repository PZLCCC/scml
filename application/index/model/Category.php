<?php
namespace app\index\model;
use think\Model;
use think\Db;


class Category extends Model{
    
    
    public function getCateInfo()
    {
        //获取所有的分类信息
        $redisObj = \redisCache::instance();//获取redis操作的对象
        $cate_info = $redisObj->get('category');
        if(!$cate_info){
            $cate_info = Db::name('category')->select();
            $redisObj->set('category',$cate_info);
        }
       
        return $cate_info;
    }
}