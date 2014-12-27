<?php 
use codeshak\classes\User;
use codeshak\classes\Redirect;

require_once "../app/init.php";

$user = new User();
if($user->loggedIn()){ 
	$user->logout();
	Redirect::to("../index.php");
}else{
	Redirect::to("../index.php");
}