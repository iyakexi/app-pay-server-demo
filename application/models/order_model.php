<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            node_model.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time
 */

// ------------------------------------------------------------------------

class Order_model extends MY_Model{
    function __construct() {
        parent::__construct();
    }

    function get_order_info($order_id){
        //从数据库中查询订单数据
        $order =  array('Subject'=>'title','TotalFee'=>'0.01');
        return $order;
    }

    function update_order_info($id, $order) {
        //TODO:: 更新订单数据
    }

}

/* End of file node_model.php */