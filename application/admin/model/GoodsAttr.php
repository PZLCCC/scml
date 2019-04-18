<?php 
namespace app\admin\model;

use think\Model;
use think\Db;
/**
 * 
 */
class GoodsAttr extends Model
{
	// 商品属性值的写入
    public function insertAttr($goods_id, $attr_ids, $attr_values)
    {

        $list = [];//保存最终要写入数据的格式
		// 去掉重复内容 重复的条件attr_id与attr_value一模一样
        $tmp = [];//保存已有的数据
        foreach ($attr_ids as $key => $value) {
            $v = $value . '-' . $attr_values[$key];
            if (in_array($v, $tmp)) {
				// 说明重复跳出当前循环
                continue;
            }
            $tmp[] = $v;
            $list[] = [
                'goods_id' => $goods_id,
                'attr_id' => $value,
                'attr_value' => $attr_values[$key]
            ];
        }
        if ($list) {
            Db::name('goods_attr')->insertAll($list);
        }
    }

}