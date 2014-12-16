<?php
require_once "core/init.php";
dir_name_autoload('fantasyproleague');

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
