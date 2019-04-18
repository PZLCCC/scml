<?php
namespace app\admin\model;
use think\Model;
use think\Db;
use think\Request;
use Model\CateModel;

class Category extends Model
{
    public function getCateInfo()
    {
        //获取所有的分类信息
        $redisObj = \redisCache::instance();//获取redis操作的对象
        $cate_info = $redisObj->get('category');
        if (!$cate_info) {
            $cate_info = Db::name('category')->select();
            $redisObj->set('category', $cate_info);
        }

        return $cate_info;
    }
    public function editCategory($data)
    {
        //不能设计分类的父分类是自己
        //$data['parent_id']代表要指定的分类的id
        if($data['id']==$data['parent_id']){
            $this -> error = '设置上级分类错误';
            return FALSE;
        }

        //不能设置自己的父分类为自己的任何一个子分类
        //获取当前修改的分类下的所有子分类

        $child = $this -> getTree($data['id']);
        foreach($child as $value){
            if($data['parent_id']== $value['id']){
                $this -> error = '上下级错乱';
                return FALSE;
            }
        }
        Category::isUpdate(TRUE)->save($data);

    }
   
    public function remove($cate_id)
    {
        //查询删除的分类下是否有子分类
        //在数据库中查询parent_id等于要删除的id
        if(Db::name('category')->where('parent_id',$cate_id)->find()){
            //说明存在子分类
            $this -> error = '存在子分类不能删除';
            return FALSE;
        }
        //没有子分类自己删除
        return Db::name('category')->delete($cate_id);
    }
    //获取格式化后的数据
    public function getTree($id=0,$is_clear=true)
    {
        //获取所有分类
        $list = $this -> all();
        //使用公共函数格式化数据
        $data = get_tree($list,$id,0,$is_clear);
        return $data;
    }
}