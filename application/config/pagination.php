<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['use_page_numbers'] = true;

$config['per_page'] = 20;
$config['num_links'] = 5;

$config['next_link'] = '下一页';
$config['prev_link'] = '上一页';

$config['first_link'] = '首页';
$config['last_link'] = '末页';

$config['next_tag_open'] = '<span class="next_page02 border_gray" href="javascript:void(0)">';
$config['next_tag_close'] = '</span>';
$config['prev_tag_open'] = '<span class="previous_page02 border_gray">';
$config['prev_tag_close'] = '</span>';

$config['first_tag_open'] = '<span class="first_page02 border_gray">';
$config['first_tag_close'] = '</span>';
$config['last_tag_open'] = '<span class="last_page02 border_gray" href="javascript:void(0)">';
$config['last_tag_close'] = '</span>';

//自定义“当前页”链接
$config['cur_tag_open'] = '<span class="on_page border_gray">';
$config['cur_tag_close'] = '</span>';

//自定义“数字”链接

$config['num_tag_open'] = '<span class="choose_page border_gray">';
$config['num_tag_close'] = '</span>';
?>