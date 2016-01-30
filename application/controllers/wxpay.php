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

class Wxpay extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->_data['module'] = 'order';
        $this->_data['title'] = '订单';
        //$this->_data['css_files'] = array('order.css');

        $this->load->model('order_model');
    }

    /**
     * 微信支付 - 统一下单返回数据接口
     * {
     * "appid": "wxb4ba3c02aa476ea1",
     * "noncestr": "549f32b19a3e65cfea9e527a2d2c1d0e",
     * "package": "Sign=WXPay",
     * "partnerid": "10000100",
     * "prepayid": "wx20150908182438d47e4998190072112949",
     * "timestamp": "1441707878",
     * "sign": "EC267FD275C50578B2D1E4DD0F12B4B2"
     * }
     * @param $order_id
     */
    function prepay_get($out_trade_no=0){
        $wxpay_config = $this->wxpay_config = $this->my_config['wxpay_config'];
        //var_dump($wxpay_config);

        $APP_ID = $wxpay_config['app_id'];            //APPID
        $APP_SECRET = $wxpay_config['app_secret'];    //appsecret
        $MCH_ID=$wxpay_config['mch_id'];
        $PARTNER_ID = $wxpay_config['partner_id'];
        $NOTIFY_URL = $wxpay_config['notify_url'];

        if (!$out_trade_no) {
            $this->display_error(400,'请求是无效的');
        }
        $order = $this->order_model->get_order_info($out_trade_no);
        if (!$order) {
            $this->display_error(1,'请求是无效的');
        }


        //STEP 1. 构造一个订单。
        $order=array(
            "body" => $order['Subject'],
            "appid" => $APP_ID,
            "device_info" => "APP-001",
            "mch_id" => $MCH_ID,
            "nonce_str" => mt_rand(),
            "notify_url" => $NOTIFY_URL,
            "out_trade_no" => $out_trade_no,
            "spbill_create_ip" => $this->input->ip_address(),
            "total_fee" => intval($order['TotalFee'] * 100),//注意：前方有坑！！！最小单位是分，跟支付宝不一样。1表示1分钱。只能是整形。
            "trade_type" => "APP"
        );
        ksort($order);

        //STEP 2. 签名
        $sign="";
        foreach ($order as $key => $value) {
            if($value&&$key!="sign"&&$key!="key"){
                $sign.=$key."=".$value."&";
            }
        }
        $sign.="key=".$PARTNER_ID;
        $sign=strtoupper(md5($sign));//echo $sign.'<br/>';exit;

        //STEP 3. 请求服务器
        $xml="<xml>\n";
        foreach ($order as $key => $value) {
            $xml.="<".$key.">".$value."</".$key.">\n";
        }
        $xml.="<sign>".$sign."</sign>\n";
        $xml.="</xml>";
        //echo $sign.'<br/>';
        $opts = array(
            'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: text/xml',
                'content' => $xml
            ),
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            )
        );
        $context  = stream_context_create($opts);
        $result = file_get_contents('https://api.mch.weixin.qq.com/pay/unifiedorder', false, $context);

        $result = simplexml_load_string($result,null, LIBXML_NOCDATA);

        //
        if ($result->return_code == 'SUCCESS' && $result->result_code == 'SUCCESS') {
            $prepay=array(
                "noncestr"=>"".$result->nonce_str,
                "prepayid"=>"".$result->prepay_id,//上一步请求微信服务器得到nonce_str和prepay_id参数。
                "appid"=>$APP_ID,
                "package"=>"Sign=WXPay",
                "partnerid"=>$MCH_ID,
                "timestamp"=>"".time(),
                "sign"=>""
            );
            ksort($prepay);
            $sign="";
            foreach ($prepay as $key => $value) {
                if($value&&$key!="sign"&&$key!="key"){
                    $sign.=$key."=".$value."&";
                }
            }
            $sign.="key=".$PARTNER_ID;
            $sign=strtoupper(md5($sign));
            $prepay['sign'] = $sign;
            $prepay['success'] = true;
        } else {
            $prepay=array(
                "success" => false,
                "noncestr"=>"",
                "prepayid"=>"",
                "appid"=>$APP_ID,
                "package"=>"Sign=WXPay",
                "partnerid"=>$MCH_ID,
                "timestamp"=>"".time(),
                "sign"=>"",
                "return_msg"=>$result->return_msg
            );
        }


        $this->response($prepay, 200);


        /*$iOSLink=sprintf("weixin://app/%s/pay/?nonceStr=%s&package=Sign%%3DWXPay&partnerId=%s&prepayId=%s&timeStamp=%s&sign=%s&signType=SHA1",$APP_ID,$input["noncestr"],$MCH_ID,$input["prepayid"],$input["timestamp"],$sign);

        echo $iOSLink;
        //在Safari中打开以便测试。
        //echo "<h1><a href='${iOSLink}'>微信支付</a></h1>";*/
    }

}

/* End of file info.php */