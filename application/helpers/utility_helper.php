<?php
/**
 * www.qinyejun.com
 *
 * Description:
 *
 * @package         www.qinyejun.com
 * @file            utility_helper.php
 * @author          QinYejun <qinyejun@me.com>
 * @copyright       Copyright (c) 2015, qinyejun, Inc.
 * @since           Version 1.0
 * @time            12:29
 */

// ------------------------------------------------------------------------
function asset_url(){
    return base_url().'assets/';
}

function mkdirs($dir, $mode = 0777, $recursive = true) {
    if (is_null($dir) || $dir === "") {
        return FALSE;
    }
    if (is_dir($dir) || $dir === "/") {
        return TRUE;
    }
    if (mkdirs(dirname($dir), $mode, $recursive)) {
        return mkdir($dir, $mode);
    }
    return false;
}

function db_to_display($text,$nl2br = true,$check_url=false) {
    //$text = preg_replace('/\[\$(.+?)\]/', "&\\1;", $text);
    //$text = str_replace('  ', '&nbsp;&nbsp;', $text);

    $text = escape_html($text,$nl2br);
    if($check_url) {
        $text = preg_replace("/(http[s]?:\/\/[^\s<>]+\.[^\s<>]+)/i",'<a target="_blank" href="\\1">\\1</a>', $text);
    }
    return $text;
}

function static_file_path($path){

}

function escape_str($str)
{
    if (function_exists('mysql_real_escape_string'))
    {
        return mysql_real_escape_string($str);
    } elseif (function_exists('mysql_escape_string'))
    {
        return mysql_escape_string($str);
    } else
    {
        return addslashes($str);
    }
}

/**
 * 格式化价格
 * @param $price
 */
function format_price($price){
    return number_format($price);
}

function format_price2($price,$decimals=0){
    return number_format($price,$decimals,'.','');
}

/**
 * 格式化时间
 *
 * @param integer $time 时间戳
 *
 * @return string
 */
function format_time($time)
{
    return date("Y-m-d H:i:s", $time);
}

/**
 * 格式化日期
 *
 * @param integer $time 时间戳
 *
 * @return string
 */
function format_date($time)
{
    return date("Y-m-d", $time);
}

/**
 * 格式化日期
 *
 * @param integer $time 时间戳
 *
 * @return string
 */
function fancy_date($time)
{
    if (date("Y-m-d") == date("Y-m-d", $time))
    {
        return "&#20170;&#22825;";
    } else if (date("Y-m-d", strtotime('yesterday')) == date("Y-m-d", $time))
    {
        return date("&#26152;&#22825;", $time);
    } else
    {
        return date("Y-m-d", $time);
    }
}

function fancy_time($time)
{
    if ($time >= strtotime('today'))
    {
        return "&#20170;&#22825; " . date("H:i", $time);
    } else if ($time >= strtotime('yesterday'))
    {
        return "&#26152;&#22825; " . date("H:i", $time);
    } else if ($time >= strtotime('this year'))
    {
        return date("n月j日 H:i", $time);
    } else
    {
        return date("Y-m-d", $time);
    }
}

/**
 * 生成随机字符串
 *
 * @param integer $length 长度
 *
 * @return string
 */
function rand_str($length = 8)
{
    $rand = "";
    $chars = array(
        "1", "2", "3", "4", "5", "6", "7", "8", "9", "0",
        "a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J",
        "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T",
        "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z");

    $count = count($chars) - 1;

    srand((double) microtime() * 1000000);

    for ($i = 0; $i < $length; $i++)
    {
        $rand .= $chars[rand(0, $count)];
    }

    return($rand);
}

/**
 * 切断给定字符串到指定长度，用mbstring函数
 *
 * @param string $str 字符串
 * @param integer $length 长度
 *
 * @return string
 */
function str_cut($str, $length = 0,$tail=true) {
    $output = mb_substr($str, 0, $length, 'utf-8');
    if($tail && mb_strlen($str,'utf-8')>$length) $output = $output.'...';
    return $output;
}

/**
 * 切断给定字符串到指定长度，一个汉字长度计为2
 *
 * @param string $str 字符串
 * @param integer $length 长度
 *
 * @return string
 */
function smart_cut($str, $len, $tail = true)
{
    $len_utf8 = mb_strlen($str, 'utf-8');

    $cur = 0;
    $i = 0;
    $output = '';

    while ($i < $len_utf8)
    {
        $char = mb_substr($str, $i++, 1, 'utf-8');

        if (ord($char) > 127)
        {
            $cur++;
        }

        $cur++;

        if ($tail && $cur > $len - 4)
        {
            $output .= ' ...';
            break;
        } elseif ($cur >= $len)
        {
            break;
        }

        $output .= $char;
    }

    return $output;
}

/**
 * 返回utf-8编码的字符串长度，一个汉字长度计为2
 *
 * @param string $str 字符串
 *
 * @return integer
 */
function smart_len($str)
{
    $len_utf8 = mb_strlen($str, 'utf-8');

    $len = 0;
    $i = 0;

    while ($i < $len_utf8)
    {
        $char = mb_substr($str, $i++, 1, 'utf-8');
        if (ord($char) > 127)
        {
            $len++;
        }

        $len++;
    }

    return $len;
}

/**
 *
 */
function db_to_editor($text)
{
    $text = str_replace("<br />", "\n", $text);
    $text = str_replace("<BR>", "\n", $text);
    $text = str_replace("&nbsp;", " ", $text);

    $text = unescape_html($text);

    return $text;
}

/**
 * 检查日期格式是否有效，格式为：YYYY-MM-DD hh:mm:ss
 * @param $date_time
 * @return bool
 */
function is_valid_datetime($date_time)
{
    if (preg_match("/^(\d{4})-(\d{2})-(\d{2}) ((?:[01])?[0-9]|2[0-3]):((?:[0-5])?[0-9]):((?:[0-5])?[0-9])$/", $date_time, $matches)) {
        if (checkdate($matches[2], $matches[3], $matches[1])) {
            return true;
        }
    }

    return false;
}

function isInteger($input){
    return(ctype_digit(strval($input)));
}

/**
 * 格式化数字,暂时把大于万级的数字转化为NK形式，如20000  20K
 * @param int $num 需要处理的整型数据
 * @return
 * @date 2013-01-23
 */
function formatNumber($num){
    $num = intval($num);
    if($num>=10000){
        return intval($num/1000).'K';
    }else{
        return $num;
    }
}

function escape_html($val, $nl2br = false)
{
    if ($val == "")
    {
        return "";
    }

    if (is_array($val))
    {
        $arr = array();
        foreach ($val as $k => $v)
        {
            $arr[$k] = escape_html($v, $nl2br);
        }
        return $arr;
//		return array_map(array(&$this, 'escape_html'), $val, $nl2br);
    }

    $val = str_replace("&#032;", " ", _strip_slashes($val));

    $val = str_replace("&", "&amp;", $val);
    $val = str_replace("<!--", "&#60;&#33;--", $val);
    $val = str_replace("-->", "--&#62;", $val);
    $val = preg_replace("/<script/i", "&#60;script", $val);
    $val = str_replace(">", "&gt;", $val);
    $val = str_replace("<", "&lt;", $val);
    $val = str_replace('"', "&quot;", $val);
    $val = str_replace('  ', '&nbsp;&nbsp;', $val);
    $val = preg_replace('/\t/', '&nbsp;&nbsp;&nbsp;&nbsp;', $val);
    //$val = str_replace( "$"				, "&#036;"        , $val );
    $val = str_replace("\r", "", $val); // Remove literal carriage returns
    //$val = str_replace( "!"				, "&#33;"         , $val );
    $val = str_replace("'", "&#39;", $val); // IMPORTANT: It helps to increase sql query safety.

    if ($nl2br)
    {
        $val = str_replace("\n", "<br />", $val); // Convert literal newlines
    }


    //$val = preg_replace("/&amp;#([0-9]+);/s", "&#\\1;", $val );

    $val = preg_replace("/&#(\d+?)([^\d;])/i", "&#\\1;\\2", $val);

    return $val;
}

function _strip_slashes($t)
{
    if (get_magic_quotes_gpc())
    {
        $t = stripslashes($t);
        //$t = preg_replace( "/\\\(?!&amp;#|\?#)/", "&#092;", $t );
    }

    return $t;
}

function escape_title($text)
{
    $text = trim($text);
    $text = unescape_html($text);
    $text = escape_html($text);
    $text = str_replace(array("\n", "\r"), '', $text);

    return $text;
}

function escape_key($key)
{
    $key = trim($key);
    $key = str_replace(array("_", "%", "*"), '', $key);

    return $key;
}

function unescape_html($val)
{
    if ($val == "")
    {
        return "";
    }

    if (is_array($val))
    {
        $arr = array();
        foreach ($val as $k => $v)
        {
            $arr[$k] = unescape_html($v);
        }
        return $arr;
//		return array_map(array(&$this, 'unescape_html'), $val);
    }

    //$val = str_replace( "&#032;", " ", $this->_strip_slashes($val) );

    $val = str_replace("&#39;", "'", $val); // IMPORTANT: It helps to increase sql query safety.
    //$val = str_replace( "!"				, "&#33;"         , $val );
    //$val = str_replace( "$"				, "&#036;"        , $val );
    $val = str_replace('&quot;', '"', $val);
    $val = str_replace("&lt;", "<", $val);
    $val = str_replace("&gt;", ">", $val);
    $val = preg_replace("/&#60;script/i", "<script", $val);
    $val = str_replace("--&#62;", "-->", $val);
    $val = str_replace("&#60;&#33;--", "<!--", $val);
    $val = str_replace("&amp;", "&", $val);

    return $val;
}

function draw_calendar($month,$year,$data){

    /* draw table */
    $calendar = '<table data-year="'.$year.'" data-month="'.$month.'">';

    /* table headings */
    $headings = array('一','二','三','四','五');
    $calendar.= '<tr class="header"><th class="sunday">日</th><th>'.implode('</th><th>',$headings).'</th><th class="saturday">六</th></tr>';

    /* days and weeks vars now ... */
    $running_day = date('w',mktime(0,0,0,$month,1,$year));
    $days_in_month = date('t',mktime(0,0,0,$month,1,$year));
    $days_in_this_week = 1;
    $day_counter = 0;
    $dates_array = array();

    /* row for week one */
    $calendar.= '<tr class="calendar-row">';

    /* print "blank" days until the first of the current week */
    for($x = 0; $x < $running_day; $x++):
        $calendar.= '<td class="calendar-day-np invalid-day"> </td>';
        $days_in_this_week++;
    endfor;

    /* keep going with days.... */
    for($list_day = 1; $list_day <= $days_in_month; $list_day++):
        $key_day = $year.'-'.$month.'-'.$list_day;
        $td_class = '';
        $td_available = '';
        $td_price = '';
        $td_attr_price = '';
        if($data && array_key_exists($key_day,$data)){
            $val = $data[$key_day];
            if($val['Available']<=0){
                $td_class .= ' invalid-day ';
            } else if($val['Available']<=5){
                $td_class .= ' over ';
                $td_available = '余位'.$val['Available'];
            } else if($val['Available']>5){
                $td_class .= ' enough ';
                $td_available = '充足';
            }
            $td_price = '¥'.format_price2($val['AdultPrice']);
            $td_attr_price = ' adault-price="'.format_price2($val['AdultPrice']).'"  child-price="'.format_price2($val['ChildPrice']).'"  room-price="'.format_price2($val['RoomPrice']).'" ';
        } else {
            $td_class = ' invalid-day ';
        }

        $calendar.= '<td class="calendar-day'.$td_class.'" data-date="'.$list_day.'" '.$td_attr_price.'>';
        /* add in the day number */
        $calendar.= '<div><span class="date" data-date="'.$list_day.'">'.$list_day.'</span><a class="dayjh" href="javascript:void(0);"></a></div>';
        $calendar.= '<div><span class="dataprace">'.$td_available.'</span></div>';
        $calendar.= '<div><span class="dataprice">'.$td_price.'</span></div>';

        /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
        $calendar.= str_repeat('<p> </p>',2);

        $calendar.= '</td>';
        if($running_day == 6):
            $calendar.= '</tr>';
            if(($day_counter+1) != $days_in_month):
                $calendar.= '<tr class="calendar-row">';
            endif;
            $running_day = -1;
            $days_in_this_week = 0;
        endif;
        $days_in_this_week++; $running_day++; $day_counter++;
    endfor;

    /* finish the rest of the days in the week */
    if($days_in_this_week < 8):
        for($x = 1; $x <= (8 - $days_in_this_week); $x++):
            $calendar.= '<td class="calendar-day-np invalid-day"> </td>';
        endfor;
    endif;

    /* final row */
    $calendar.= '</tr>';

    /* end the table */
    $calendar.= '</table>';

    /* all done, return result */
    return $calendar;
}

function url_with_param($base_url,$param=array(),$except_key=''){
    $url = $base_url;
    foreach($param as $key=>$val) {
        if(!$val) continue;
        if($key!=$except_key){
            $url .= "&$key=$val";
        }
    }
    return $url;
}

function draw_advertise($section,$static_domain){
    if($section['Pic']){?>
    <div class="side-box"><div class="content-img">
    <?php
        if($section['AdUrl']){ ?>
        <a target="_blank" href="<?= $section['AdUrl'] ?>">
        <?php }?>
        <img src="<?= $static_domain.$section['Pic'] ?>" alt="">
        <?php if($section['AdUrl']){ ?></a> <?php } ?>
    </div></div>
    <?php }
}

    /**
     * 字符串半角和全角间相互转换
     * @param string $str 待转换的字符串
     * @param int $type TODBC:转换为半角；TOSBC，转换为全角
     * @return string 返回转换后的字符串
     */
function convertStrType($str, $type='TOSBC') {
    $dbc = array(
        '０' , '１' , '２' , '３' , '４' ,
        '５' , '６' , '７' , '８' , '９' ,
        'Ａ' , 'Ｂ' , 'Ｃ' , 'Ｄ' , 'Ｅ' ,
        'Ｆ' , 'Ｇ' , 'Ｈ' , 'Ｉ' , 'Ｊ' ,
        'Ｋ' , 'Ｌ' , 'Ｍ' , 'Ｎ' , 'Ｏ' ,
        'Ｐ' , 'Ｑ' , 'Ｒ' , 'Ｓ' , 'Ｔ' ,
        'Ｕ' , 'Ｖ' , 'Ｗ' , 'Ｘ' , 'Ｙ' ,
        'Ｚ' , 'ａ' , 'ｂ' , 'ｃ' , 'ｄ' ,
        'ｅ' , 'ｆ' , 'ｇ' , 'ｈ' , 'ｉ' ,
        'ｊ' , 'ｋ' , 'ｌ' , 'ｍ' , 'ｎ' ,
        'ｏ' , 'ｐ' , 'ｑ' , 'ｒ' , 'ｓ' ,
        'ｔ' , 'ｕ' , 'ｖ' , 'ｗ' , 'ｘ' ,
        'ｙ' , 'ｚ' , '－' , '　' , '：' ,
        '．' , '，' , '／' , '％' , '＃' ,
        '！' , '＠' , '＆' , '（' , '）' ,
        '＜' , '＞' , '＂' , '＇' , '？' ,
        '［' , '］' , '｛' , '｝' , '＼' ,
        '｜' , '＋' , '＝' , '＿' , '＾' ,
        '￥' , '￣' , '｀','【','】'
    );
    $sbc = array( //半角
        '0', '1', '2', '3', '4',
        '5', '6', '7', '8', '9',
        'A', 'B', 'C', 'D', 'E',
        'F', 'G', 'H', 'I', 'J',
        'K', 'L', 'M', 'N', 'O',
        'P', 'Q', 'R', 'S', 'T',
        'U', 'V', 'W', 'X', 'Y',
        'Z', 'a', 'b', 'c', 'd',
        'e', 'f', 'g', 'h', 'i',
        'j', 'k', 'l', 'm', 'n',
        'o', 'p', 'q', 'r', 's',
        't', 'u', 'v', 'w', 'x',
        'y', 'z', '-', ' ', ':',
        '.', ',', '/', '%', '#',
        '!', '@', '&', '(', ')',
        '<', '>', '"', '\'','?',
        '[', ']', '{', '}', '\\',
        '|', '+', '=', '_', '^',
        '¥','~', '`','[',']'
    );
    if($type == "TODBC"){
        return str_replace( $sbc, $dbc, $str ); //半角到全角
    }elseif($type == "TOSBC"){
        return str_replace( $dbc, $sbc, $str ); //全角到半角
    }else{
        return false;
    }
}

function is_ie(){
    if (isset($_SERVER['HTTP_USER_AGENT']) &&
        (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
        return true;
    else
        return false;
}

function generate_code($length = 6) {
    return substr(str_shuffle("123456789123456789123456789"), 0, $length);
}

//php获取中文字符拼音首字母
function get_first_charter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return null;
}