<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            visa.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            15:37
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
// ------------------------------------------------------------------------

class Index extends MY_Controller {
    function __construct() {
        parent::__construct();
        $this->_data['module'] = 'home';
        $this->_data['title'] = '首页';
    }

    function index_get(){
        $this->puts_result (array('msg'=>'code by qinyejun'));
    }

}

/* End of file visa.php */