<?php
require_once "../core/init.php";
dir_name_autoload("login");

$user = new User();
if($user->loggedIn()){
	$user->logout();
	Redirect::to("../index.php");
}