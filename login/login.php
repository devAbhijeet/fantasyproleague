<?php
use codeshak\classes\Input;
use codeshak\classes\Token; 
use codeshak\classes\Validation;
use codeshak\classes\User;
use codeshak\classes\Redirect;

require_once "../app/init.php";
 
if(Input::exists()){
	if(Token::check(Input::get("token"))){

		$validate = new Validation();
		$validate = $validate->check($_POST,array(
			"username" => array("required"=>true),
			"password" => array("required"=>true)
		)); 

		if($validate->passed()){
			$user = new User();
			$login = $user->login(Input::get("username"),Input::get("password"));  

			if($login){
				Redirect::to("../index.php"); 
			}else{
				echo "Loggin error";
			}
		}else{
			$valErrors = $validate->errors(); 
		}
	}	
} 
?>

<div class="signin-container">
	<form action="" method="post" class="form-sign-in" id="sign-in-account" novalidate>
		<fieldset>
			<legend>LogIn</legend>
			<div class="form-error"></div>
			<div class="form-field">
				<label for="username">Username</label>
				<input type="text" name="username" id="username" class="validate-locally" maxlength="30">
				<span class="errors">
					<?php if(isset($valErrors["username"])){echo implode(", ",$valErrors["username"]);}?>
				</span>
			</div>
			<div class="form-field">
				<label for="password">Password</label>
				<input type="password" name="password" id="password" class="validate-locally" maxlength="32">
				<span class="errors">
					<?php if(isset($valErrors["password"])){echo implode(", ",$valErrors["password"]);}?>
				</span>
			</div>
			<input type="hidden" name="token" value="<?php echo Token::generate();?>">
			<div class="form-field">
	            <input type="submit" class="submit" value="Sign In" >
	        </div> 
	        <div class="forget">
	        	<small>
	        		Forgot
	        		<a href="../recover.php?mode=username">Username</a>&nbsp/
	        	</small>
	        	<small>
	        		<a href="../recover.php?mode=password">Password</a>
	        	</small>
	        </div>
		</fieldset>
	</form>
</div>