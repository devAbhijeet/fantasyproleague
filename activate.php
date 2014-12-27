<?php
use codeshak\classes\User;
use codeshak\classes\Redirect;
use codeshak\classes\Input; 

require_once "app/init.php";

$user = new User();

if($user->loggedIn()){
	Redirect::to("index.php");
}

if(Input::exists("get")){
	$email     = Input::get("email");
	$emailcode = Input::get("emailcode");
	
	if($user->find($email,"email")){
		if($user->data()->emailcode===$emailcode){
			try{
				$user->aoUpdate( 
					[
					"email"     => $email,
					"active"    => 0
					],
					[
					"active" => 1
					]);

				Redirect::to("login/login.php");

			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else{
			Redirect::to("login/index.php");
		}
	}else{
		Redirect::to("login/index.php");
	}
}else{
	Redirect::to("login/index.php"); 
} 
