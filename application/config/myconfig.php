<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            myconfig.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            15:17
 */

// ------------------------------------------------------------------------

$config['js_css_suffix'] = '201505211238';
$config['web_domain'] = 'http://www.qinyejun.com';
$config['api_domain'] = 'http://api.qinyejun.com';
$config['static_domain'] = 'http://static.qinyejun.com';

//支付宝配置
$config['alipay_config'] = array(
        'partner' => '',
        'key' => '',
        'seller_email' => '',
        'sign_type' => 'MD5',
        'input_charset' => 'utf-8',
        'cacert' => 'cacert.pem',
        'transport' => 'http',
        'return_url' => 'http://www.yourdomain.com/order/alipay_return',
        'notify_url' => 'http://www.yourdomain.com/order/alipay_notify'
);

//微信支付配置
$config['wxpay_config'] = array(
    'app_id' => 'wx00000000',
    'app_secret' => '',
    'mch_id' => '',
    'partner_id' => '',
    'notify_url' => 'http://www.yourdomain.com/order/wxpay_notify'
);

$config['cookie'] = array(
    'prefix'=>'ckpf_',
    'domain'=>'ckpf',
    'expire'=>14 * 24 * 3600
);