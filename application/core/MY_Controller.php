<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            MY_Controller.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            15:14
 *
 * 错误码	错误描述
 * 400	Bad Request ：请求是无效的。 1.参数不正确 2.必需的参数为空
 * 401	Unauthorized ：身份验证失败。
 * 403	Forbidden ：请求被拒绝，未授权。1.签名验证失败 2.用户没有权限做相应操作
 * 404	Not Found ：请求的URI无效或请求的资源无效。1. 根据当前条件查询结果为空
 * 500	Internal Server Error : 因为意外情况，服务器不能完成请求。1.查询db出现错误
 */

// ------------------------------------------------------------------------

require_once dirname(__FILE__)."/../config/myconfig.php";

require_once APPPATH . "models/alipay/alipay_notify.class.php";
require_once APPPATH . "models/alipay/alipay_submit.class.php";

require_once 'REST_Controller.php';

class MY_Controller  extends REST_Controller {
    public $web_domain;
    public $static_domain;

    public $my_config;
    public $alipay_config;
    public $config_express;
    public $config_sms;

    function __construct() {
        parent::__construct();
        //$this->load->library('session');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->helper('date');
        $this->config->load('myconfig',true);

        $this->my_config = $this->config->item('myconfig');

        $this->web_domain = $this->_data['web_domain'] = $this->my_config['web_domain'];
        $this->alipay_config = $this->_data['alipay_config'] = $this->my_config['alipay_config'];

        if(ENVIRONMENT == 'production')
            $this->check_signature();;


    }

    function check_signature(){
        // TODO:: 安全验证代码
        return true;
    }

    function check_auth(){
        return true;
    }

    /**
     * 对api数据进行 JSON 编码
     * @param $arr :array
     * @param $total: int
     */
    function puts_result($arr, $total=FALSE) {
        $result = array(
            'success'=>true,
            'errorCode'=>'0',
            'total'=>0,
            'data'=>''
        );
        if(is_numeric($total))
            $result['total'] = $total;
        if(!$arr || $arr==''){
            $arr = NULL;
        }
        if ($arr && !is_array($arr)) {
            $result['msg'] = $arr;
        }else {
            $result['data'] = $this->array_key_lcfirst($arr);
        }

        //$this->output->set_content_type('application/json')->set_output(json_encode($result));
        $this->response($result, 200);
        if(ENVIRONMENT == 'development' && $this->input->get('debug', TRUE)){
            //echo '<pre>';print_r($result);echo '</pre>';
        }
    }

    function puts_result_array($arrData, $arrResult , $total=FALSE) {
        $result = array(
            'success'=>true,
            'errorCode'=>'0',
            'data'=>''
        );
        if(is_numeric($total))
            $result['total'] = $total;
        if (is_array($arrResult)) {
            $result = array_merge($result,$arrResult);
        }
        if(!$arrData || $arrData==''){
            $arrData = NULL;
        }
        if ($arrData && !is_array($arrData)) {
            $result['msg'] = $arrData;
        }else {
            $result['data'] = $this->array_key_lcfirst($arrData);
        }
        $this->response($result, 200);
    }


    function array_key_lcfirst($arr){
        if(!is_array($arr)) return $arr;
        foreach($arr as $k=>$v) {
            if(is_array($v)){
                $v = $this->array_key_lcfirst($v);
            }
            $key = !is_numeric($k)?lcfirst($k):$k;
            if( strtolower($key)=="id") $key = "id";
            $arr[$key] = $v;
            if($key!=$k)
                unset($arr[$k]);

        }
        return $arr;
    }

    /**
     * @param $code :错误码
     */
    function  display_error($code,$msg=""){
        if($code==404){
            $this->puts_result(NULL);
        }else{
            $arr_err = array("success"=>false,"errorCode"=>"$code","msg"=>$msg,"total"=>0);
            //$this->output->set_content_type('application/json')->set_output(json_encode($arr_err));
            $this->response($arr_err, 200);
        }
        exit;
    }

    function result_recommend($recommend){
        if(!$recommend || !is_array($recommend)) return null;
        $result = array();
        foreach($recommend as $r){
            $item = array();
            $item['ID'] = $r['ID'];
            $item['Title'] = $r['Title'];
            $item['SectionID'] = $r['SectionID'];
            $item['Feature'] = $r['Feature'];
            $item['Price'] = format_price2($r['Price']);
            $item['MarketPrice'] = format_price2($r['MarketPrice']);
            $item['Url'] = $r['Url'];

            $no_image = false;
            if($r['MobilePic']) {
                $pic = $this->static_domain.$r['MobilePic'];
                $width = $r['MobileWidth'];
                $height = $r['MobileHeight'];
            } else if($r['Pic']) {
                $pic = $this->image_url($r['Pic']);
                $width = $r['Width'];
                $height = $r['Height'];
            } else {
                $no_image = true;
                $width = $height = 0;
            }
            $item['Img'] = $no_image ? null :  array('Url'=>$pic,'Width'=>$width?$width:0,'Height'=>$height?$height:0);

            $pids = array();
            if(preg_match('/\/(visa|tour|ticket|insurance|info)\/(list|search|detail)\/?\?(p|n)?id=(\d+)\w*/i',$r['Url'],$pids)){
                $module = $pids[1];
                $page = $pids[2];
                $id = $pids[4];
                if($module=='info') $module = 'article';

                $item['Module'] = array('Name'=>$module,'Action'=>$page=='search'?'list':$page,'ID'=>$id);
            } else {
                $item['Module'] = array('Name'=>'','Action'=>'','ID'=>0);
            }

            array_push($result,$item);

        }

        return $result;
    }

    function  result_order($order) {
        $r = array();
        $r['id'] = $order['OrderID'];
        $r['orderType'] = $order['OrderType'];
        $r['title'] = $order['Subject'];
        $r['content'] = $order['Desc'];
        $r['url'] = $order['ShowUrl'];
        $r['createdAt'] = format_time($order['CreatedTime']);
        $r['state'] = $order['OrderStatus']?$order['OrderStatus']:'处理中';
        $r['contact'] = $order['ContactName'].'('.substr_replace($order['ContactPhone'],'****',3,4).')';
        $r['price'] = $order['TotalFee'];
        $r['paid'] = ($order['TradeStatus']=='TRADE_SUCCESS'||$order['TradeStatus']=='TRADE_FINISHED' ||  $order['TotalFee'] <= 0.009) ? true : false;
        $r['refID'] = $order['RefID'];
        return $r;
    }

    function image_url($path){
        return $path?$this->static_domain.$path:'';
    }

}

/* End of file MY_Controller.php */