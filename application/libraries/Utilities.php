<?php

/**
 * api.qinyejun.net
 *
 * Description: 通用方法集合
 *
 * @package         api.qinyejun.net
 * @file            Utilities.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun
 * @since           Version 1.0
 * @date            13-12-18
 * @time            11:32
 */


class Utilities {

    //配置文件
    var $my_config;


    function __construct($my_config) {
        $this->my_config = $my_config;
    }

    /**
     * 返回用户头像完整url
     * @param $url
     */
    function face_url($url){
        if(!$url) return '';
        if(strpos($url, 'http://')!==FALSE) return $url;
        return trim($this->my_config['site']['static_path'],'/').'/'.$this->my_config['site']['data_url'].'/faces/'.trim($url,'/');
    }

    /**
     * 返回图片完整url
     * @param $photo_path 数据库中图片路径
     */
    function photo_url($photo_path, $ext=".jpg"){
        if(!$photo_path) return '';
        return rtrim($this->my_config['site']['static_path'],'/').'/'. trim($this->my_config['site']['data_url'],'/'). '/' .$photo_path.$ext;
    }

    /**
     * 把timestamp转换成格式化的string
     * @param $time_stamp   int
     * @return string
     */
    function timestamp_formatter($timestamp){
        if(!is_numeric($timestamp)) return FALSE;
        return date('Y-m-d H:i:s',$timestamp);
    }

    /**
     *
     */
    function db_to_display($text, $parse_bbcode = true, $parse_smiles = true, $resize = false) {

        // replace [$nbsp]...
        $text = preg_replace('/\[\$(.+?)\]/', "&\\1;", $text);
        $text = str_replace('  ', '&nbsp;&nbsp;', $text);

        return $text;
    }


    function check_email_valid($email){
        if(empty($email)){
            return FALSE;
        }
        if(!empty($email) && !preg_match("/^([a-zA-Z0-9\-\_\.]+)\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,4}|[0-9]{1,4})(\]?)$/", $email)){
            return FALSE;
        }
        return TRUE;
    }
    
}

?>
