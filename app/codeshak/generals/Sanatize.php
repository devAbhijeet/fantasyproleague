<?php namespace codeshak\generals;

class Sanatize{
	public static function htmlEntities($string){ 
		return htmlentities($string,ENT_QUOTES,"UTF-8");
	} 

	public static function arraySanatize(&$value){
		$value = strip_tags(mysql_real_escape_string(stripslashes($value)));
	}
}