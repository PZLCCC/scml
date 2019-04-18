<?php 
namespace app\admin\validate;

use think\Validate;

/**
 * 商品的验证器
 */
class Goods extends Validate
{

	// 验证规则
	protected $rule = [
		'goods_name|商品名称' => 'require',
		'cate_id' => 'require|gt:0',
		'shop_price' => 'require|gt:0',
		'market_price' => 'require|checMarketPrice'
	]; 
	// 检查市场价格
	public function checMarketPrice($value, $rule, $data)
	{
		if ($data['shop_price'] > $data['market_price']) {
			return false;
		}
		return true;
	}

}
?>