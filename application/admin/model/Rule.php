<?php
namespace app\admin\model;
use think\Model;

class Rule extends Model
{
    public function editRule($data)
    {
        if($data['id']== $data['parent_id'])
        {
            $this->error = '设置上级权限错误';
            return FALSE;
        }
        $child = $this->getRules($data['id']);
        foreach ($child as $value) {
            if($data['parent_id'] == $value['id'] ){
                $this->error = '上下级错乱';
                return FALSE;
            }

        }
        Rule::isUpdate(TRUE)->allowField(true)->save($data);
    }
    public function getRules($id =0 ,$is_clear=true)
    {
        //获取所有的分类信息
        $list = $this->all();
        //对分类信息格式化
        $data = get_tree($list,$id,0,$is_clear);
        return $data;

        
    }
}