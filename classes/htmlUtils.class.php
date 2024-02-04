<?php 

/*
 * 
 * Utils for Processing / Converting HTML rendered markup
 * 
 * 
 */

class htmlUtils
{
    
    public function __construct() {}
    

    public static function convertToPlainText($str)
    {
        $str = html_entity_decode(strip_tags($str), ENT_QUOTES, 'utf-8');
        return trim(strip_tags(preg_replace("/&#?[a-z0-9]+;/i"," ",$str)));
    }

    public function StripLinks($str) {
        return preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $str);
    }

}




?>

