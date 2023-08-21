<?php
class  Filter {
    
    public static function clear($string) {
        $chars = [' ',',','.','-','_','/','¡','!','°','|','¬','%','"',"'",'$','&','>','<'];
        $search = ['/\s+/','/\t+/','/\n\r+/'];
        $string = preg_replace($search,' ',$string);
        //$string = trim(iconv("UTF-8","ISO-8859-1",$string)," \t\n\r\0\x0B\xA0");
        foreach($chars as $char){$string = trim($string, $char);}
        return $string;
    }
    
    public static function drop_charset($string) {
        $chars = ['/[áàâãªä]/u'=>'a','/[ÁÀÂÃÄ]/u'=>'A','/[ÍÌÎÏ]/u'=>'I','/[íìîï]/u'=>'i','/[éèêë]/u'=>'e','/[ÉÈÊË]/u'=>'E','/[óòôõºö]/u'=>'o','/[ÓÒÔÕÖ]/u'=>'O','/[úùûü]/u'=>'u','/[ÚÙÛÜ]/u'=>'U'/*,'/ñ/'=>'n','/Ñ/'=>'N'*/];
        $string = preg_replace(array_keys($chars), array_values($chars), $string);
        return $string;
    }
    
    public static function clear_array($array,$keys) {
        foreach ($array as $key => $value) {
            if(is_numeric($key) || in_array($key, $keys)) unset($array[$key]);
        } return $array;
    }
}