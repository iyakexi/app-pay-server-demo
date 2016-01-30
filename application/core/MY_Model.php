<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            MY_Model.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            14:59
 */

// ------------------------------------------------------------------------

class MY_Model extends CI_Model{

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function update_entity($table_name, $where, $rows) {
        $this->db->where($where);
        $this->db->update($table_name, $rows);
    }

    function get_section_recommen($section_id){
        $limit = 20;
        $offset = 0;
        switch($section_id){
            case 101:
                $limit = 16;
                break;
            case 102:
                $limit = 20;
                break;
            default:
                $limit = 20;
        }
        $result = $this->db->order_by('OrderNo asc, ID asc')->get_where('recommend', array('SectionID' => $section_id),$limit,$offset)->result_array();
        return !empty($result) ? $result : FALSE;
    }

}

/* End of file MY_Model.php */