<?php 
	// 快递公司的编号 手动查询。实际使用项目需要记录每一家快递公司的代号
$com = 'yt';
	// 快递的运单号
$no = '804764349180115273';
	// 身份标识
$key = '957023a0bef27dda682edf1ef42b71c9';

	// 组装请求地址
$url = 'http://v.juhe.cn/exp/index?key=' . $key . '&com=' . $com . '&no=' . $no;
	// 发送http协议的请求
$res = file_get_contents($url);

echo $res;
?>