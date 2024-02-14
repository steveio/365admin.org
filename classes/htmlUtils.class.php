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
        return strip_tags(html_entity_decode($str, ENT_QUOTES, 'utf-8'));
    }

    public static function stripLinks($str) {
        return preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $str);
    }

    public static function convertCkEditorFont2Html($text,$title)
    {

        $text = html_entity_decode($text, ENT_QUOTES, 'utf-8');
        
        $text = preg_replace('/<span style=\"font-size: 12px;\">/','',$text);
        return $text = preg_replace('/<\/span>/','',$text);
        
    }
}




?>