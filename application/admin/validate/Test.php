<?php 
namespace app\admin\validate;

use think\Validate;
/**
 * 
 */
class Test extends Validate
{
	// 指定验证规则时即设置rule属性
	protected $rule = [
		'name' => 'require|checkName:6,27',
		'email|邮箱' => 'email',
		'age' => 'gt:19'
	];
	// 错误信息设置
	protected $message = [
		// 每一个元素的值为错误的提示内容
		// 下标需要指定某一个数据对应的规则
		'name.checkName' => '名称长度错误',
		'name.require' => '名称必须填写',
	];
	// 定义场景 数组下标为场景名称 名称自定义 对应的元素的内容为校验的数据
	protected $scene = [
		'add' => ['name', 'email'],
		'edit' => ['name', 'age'],
	];

	public function checkName($value, $rule, $data)
	{
		// 检查数据内容是否存在
		if (!$value) {
			return false;
		}
		// 提取$rule中额外指定的规则
		$tmp = explode(',', $rule);
		if (mb_strlen($value) <= $tmp[0] || mb_strlen($value) > $tmp[1]) {
			return false;
		}
		return true;
	}

}

?>