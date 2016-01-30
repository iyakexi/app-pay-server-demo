<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ParamValidate
 *
 * @author 780-3
 */
class ParamDataValidate {
    //put your code here

    /**
     *
     * @description 参数验证
     * 数组批量验证 
     * @param $params 要验证的数组
     * @param $validateStr 校验的条件，多重校验以 | 分隔
     * @return BOOL 值
     */
    function paramArrayValidate($params, $validateStr){
        return TRUE;
        $splitStrs = explode('|',$validateStr);
        foreach ($params as $param) {
            foreach ($splitStrs as $splitStr) {
                if(!$this->isEnumType($param, $splitStr)){     
                    return FALSE;
                }
            }
        }
        return TRUE;
    }
    
    function isEnumType($param, $str){
        $validateStrEnum = array('required','numeric','trim');
        if (in_array($str, $validateStrEnum)){
            if($str == 'required'){    
                if(is_null($param) || $param == ''){
                    return FALSE;
                }
                 return TRUE;
            }  elseif ($str == 'numeric') {
                if(!is_numeric($param))return FALSE;
                 return TRUE;
            }  elseif ($str == 'trim') {
                return TRUE;
            }  else {
                return FALSE;
            }
        }  else {
            return FALSE;
        }

    }
    
}

?>
