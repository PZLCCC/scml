<?php
namespace app\admin\model;
use think\Model;
use think\Db;

class Attribute extends Model{

    // 根据类型id查询属性信息
    public function getAttrByTypeID($type_id)
    {
        $list = Db::name('Attribute')->where('type_id', $type_id)->select();
		// 当属性为列表选择格式化属性的默认值
        foreach ($list as $key => $value) {
            if ($value['attr_input_type'] == 2) {
				// 当前属性值录入方式为列表选择
                $list[$key]['attr_values'] = explode(',', $value['attr_values']);
            }
        }
        return $list;
    }
    public function remove($Attribute_id)
    {
        return $this->where('id',$Attribute_id)->delete();
    }
    public function listData()
    {
        return $this->alias('a')->join('shop_type b','a.type_id=b.id','left')->field('a.*,b.type_name')->select();
    }
    public function addAttr($data)
    {   
        if($data['attr_input_type'] == 1){
            unset($data['attr_values']);
        }else{
            if(!$data['attr_values']){
                $this->error='列表选择默认为必填';
                return FALSE;
            }

        }
        return $this->allowField(TRUE)->save($data);
    }
}