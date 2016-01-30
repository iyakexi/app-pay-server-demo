<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            info.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            17:58
 */

// ------------------------------------------------------------------------
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

require_once APPPATH . "models/alipay/alipay_notify.class.php";
require_once APPPATH . "models/alipay/alipay_submit.class.php";
require_once APPPATH . "models/WxPayPubHelper/WxPayPubHelper.php";


class Order extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->_data['module'] = 'order';
        $this->_data['title'] = '订单';
        //$this->_data['css_files'] = array('order.css');

        $this->config->load('myconfig');
        $this->load->model('order_model');
    }

    /**
     * App支付Notify
     */
    function alipay_notify_app(){
        $alipay_config_app = $this->config->item('alipay_config_app');

        $alipayNotify = new AlipayNotify($alipay_config_app);
        $verify_result = $alipayNotify->verifyNotify();

        if($verify_result) {//验证成功

            $out_trade_no = $_POST['out_trade_no'];
            //支付宝交易号
            $trade_no = $_POST['trade_no'];
            //交易状态
            $trade_status = $_POST['trade_status'];

            $this->log_result('alipay_notify',"【支付宝回调App】:\n".json_encode($_POST)."\n");
            if($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序

                $order = $this->order_model->get_order_info($out_trade_no);

                if($order['TradeStatus'] != 'TRADE_FINISHED' && $order['TradeStatus'] != 'TRADE_SUCCESS'){
                    $data = array('TradeStatus'=>$trade_status,'TradeNo'=>$trade_no,'PayTime'=>time(),'PayType'=>'alipay');
                    $this->order_model->update_order_info($out_trade_no,$data);
                }
            }
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
            echo "success";		//请不要修改或删除
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";

            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }

    }

    /**
     * 微信支付Notify
     */
    function wxpay_notify(){
        //使用通用通知接口
        $notify = new Notify_pub();

        //存储微信的回调
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];
        $notify->saveData($xml);

        //验证签名，并回应微信。
        //对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，
        //微信会通过一定的策略（如30分钟共8次）定期重新发起通知，
        //尽可能提高通知的成功率，但微信不保证通知最终能成功。
        if($notify->checkSign() == FALSE){
            $notify->setReturnParameter("return_code","FAIL");//返回状态码
            $notify->setReturnParameter("return_msg","签名失败");//返回信息
        }else{
            $notify->setReturnParameter("return_code","SUCCESS");//设置返回码
        }
        $returnXml = $notify->returnXml();
        echo $returnXml;

        //==商户根据实际情况设置相应的处理流程，此处仅作举例=======

        //以log文件形式记录回调信息

        $log_type="wxpay_notify";//log文件路径
        $this->log_result($log_type,"【接收到的notify通知】:\n".$xml."\n");

        if($notify->checkSign() == TRUE) {
            if ($notify->data["return_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $this->log_result($log_type,"【通信出错】:\n".$xml."\n");
            } elseif ($notify->data["result_code"] == "FAIL") {
                //此处应该更新一下订单状态，商户自行增删操作
                $this->log_result($log_type,"【业务出错】:\n".$xml."\n");
            } else {
                //此处应该更新一下订单状态，商户自行增删操作
                $this->log_result($log_type,"【支付成功】:\n".$xml."\n");
                $out_trade_no = $notify->data['out_trade_no'];
                $trade_no = $notify->data['transaction_id'];

                $order = $this->order_model->get_order_info($out_trade_no);
                //echo "trade_no: $trade_no<br/> out_trade_no: $out_trade_no";print_r($order);
                if($order['TradeStatus'] != 'TRADE_FINISHED' && $order['TradeStatus'] != 'TRADE_SUCCESS'){
                    $data = array('TradeStatus'=>'TRADE_SUCCESS','TradeNo'=>$trade_no,'PayTime'=>time(),'PayType'=>'wxpay');
                    $this->order_model->update_order_info($out_trade_no,$data);
                }
            }

        }

    }

    function  log_result($log_type,$word) {
        $data = array(
            'LogType' => $log_type,
            'CreatedAt' => time(),
            'Log' => strftime("%Y-%m-%d-%H：%M：%S",time())."\n".$word."\n\n"
        );
        //$this->db->insert('sys_logs', $data);
    }

}

/* End of file info.php */