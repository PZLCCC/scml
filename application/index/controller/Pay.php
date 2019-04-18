<?php
namespace app\admin\controller;
use think\Request;
use think\Model;
use think\Db;

class Pay extends Common{

    //支付宝同步回调
    public function alipayReturn()
    {
        require_once("../extend/alipay/config.php");
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';


        $arr = $_GET;
        $alipaySevice = new \AlipayTradeService($config);
        $result = $alipaySevice->check($arr);

    /* 实际验证过程建议商户添加以下校验。
    1、商户需要验证该通知数据中的out_trade_no是否为商户系统中创建的订单号，
    2、判断total_amount是否确实为该订单的实际金额（即商户订单创建时的金额），
    3、校验通知中的seller_id（或者seller_email) 是否为out_trade_no这笔单据的对应的操作方（有的时候，一个商户可能有多个seller_id/seller_email）
    4、验证app_id是否为该商户本身。
            */
            if (!$result) {//验证成功
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//请在这里加上商户的业务逻辑程序代码
                echo '验证失败';exit();
            }
	//——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
    //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表

	//商户订单号
            $out_trade_no = htmlspecialchars($_GET['out_trade_no']);

	//支付宝交易号
            $trade_no = htmlspecialchars($_GET['trade_no']);

	//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
    $order_info = Db::name('order')->where('order_sn',$out_trade_no)->find();
    if(!$order_info || $order_info['status'] == 1){
        return '订单错误';
    }
    //设置用户的订单
    Db::name('order')->where('order_sn',$out_trade_no)->setField('status','1');
    }

    //支付宝的异步回调
    public function alipayNolify()
    {
        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';

        $arr = $_POST;
        $alipaySevice = new \AlipayTradeService($config);
        $alipaySevice->writeLog(var_export($_POST, true));
        $result = $alipaySevice->check($arr);
        if (!$result) {
            return 'fail';
        }
        $out_trade_no = $_POST['out_trade_no'];
	    //支付宝交易号
        $trade_no = $_POST['trade_no'];
	    //交易状态
        $trade_status = $_POST['trade_status'];
        if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //表示当前用户完成支付但在支付宝中的订单还未完成，后续可以发起退款等操作
            $order_info = Db::name('order')->where('order_sn',$out_trade_no)->find();
            if(!$order_info){
                return 'fail';
            }
            if($order_info['status'] == 0){
                Db::name('order')->where('order_sn',$out_trade_no)->setField('status',1);
                return 'fail';
            }
            echo "success";
        }
    } 
      
}