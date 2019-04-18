<?php
namespace app\index\controller;
use think\Request;
use think\Db;

class Order extends Common{
    public function check()
    {
        //检查用户的登录
        $this -> checkUserLogin();
        $data = model('Cart')->getCartList();
        $this->assign('data',$data);
        $total = model('Cart')->getTotal($data);
        $this->assign('total',$total);
        return $this->fetch();
    }

    //下单操作
    public function pay()
    {
        $this->checkUserLogin();
        $order_info = model('Order')->order();
        if($order_info === FALSE){
            $this->error($model->getError());
        }
        if($order_info['pay'] ==1){
            $this->alipay($order_info);
        }
    }
    public function alipay($order_info){
        require_once '../extend/alipay/config.php';
        require_once '../extend/alipay/pagepay/service/AlipayTradeService.php';
        require_once '../extend/alipay/pagepay/buildermodel/AlipayTradePagePayContentBuilder.php';

        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = trim($order_info['order_sn']);

        //订单名称，必填
        $subject = trim('test-alipay');

        //付款金额，必填
        $total_amount = trim($order_info['total_money']);

        //商品描述，可空
        $body = trim('test-desc');

	    //构造参数
        $payRequestBuilder = new \AlipayTradePagePayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setOutTradeNo($out_trade_no);

        $aop = new \AlipayTradeService($config);

        /**
         * pagePay 电脑网站支付请求
         * @param $builder 业务参数，使用buildmodel中的对象生成。
         * @param $return_url 同步跳转地址，公网可以访问
         * @param $notify_url 异步通知地址，公网可以访问
         * @return $response 支付宝返回的信息
         */
        $response = $aop->pagePay($payRequestBuilder, $config['return_url'], $config['notify_url']);

	    //输出表单
        var_dump($response);
    }
}