<?php
class Redirect{
	public static function to($location = null){
		if($location){
			$loc = end(explode("/",$location));
			switch ($loc) {
				case 404:
					header("HTTP/1.0 404 Not Found");
					include "../includes/error/404.php";
					exit(); 
				break;

				case 'index.php':
					header("Location: {$location}");
					exit();
				break;

				case 'login.php':
					header("Location: {$location}");
					exit();
				break;
				
				default:
					return false;
				break;
			}
		}
	}
}
