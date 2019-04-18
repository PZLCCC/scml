<?php
namespace app\admin\model;
use think\Model;


class Type extends Model
{
    public function addType($data)
    {
        return $this->allowField(true)->save($data);
    }
    public function listData()
    {
        return $this -> all();
    }
    public function remove($type_id)
    {
        return $this->where('id',$type_id)-> delete();
    }
    public function editType($data){
        return $this->allowField(true)->isUpdate(true)->save($data);
    }
}