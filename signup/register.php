<?php
require_once "../core/init.php";
dir_name_autoload("signup");
if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate  = new Validation();
		$validate  = $validate->check($_POST,array(
			"email" 		  => array(
				"maxLength"   => 32,
				"regex"       => "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i"
			),
			"username" 		  => array(
				"maxLength"   => 32,
				"minLength"   => 3,
				"alnumdash"   => "/^[a-z0-9_\-]+$/i",
				"unique"      => "users"
			),
			"password"		  => array(
				"minLength"   => 6 
			),
			"confirmpassword" => array(
				"matches"     => "password"
			) 
		)); 
		 
		if($validate->passed()){
			
			array_walk($_POST,"Sanatize::arraySanatize");
			
			$user = new User();
			$salt = Hash::salt(32);

			try{
				$user->create(array(
					"email"    => Input::get("email"),
					"username" => Input::get("username"),
					"password" => Hash::make(Input::get("password"),$salt),
					"salt"     => $salt,
					"joined"   => "NOW()" 
				));
				Session::flash("success","You are registered successfully");
				Redirect::to("index.php");
			}catch(Exception $e){
				echo $e->getMessage();
			}
		}else{
			$valErrors = $validate->errors();
		}
	}
}

?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title> 
			Classic Form
		</title>
	</head>
	<body>
		<div class="wrapper">
			<div class="form-container">
				<form action="" method="post" name="form-register" class="form-register" id="form-new-account" novalidate >
					<fieldset>
					<legend>Register</legend>
						<div class="form-field">
							<label for="email">Email Address</label>
							<input type="email" name="email" id="email" maxlength="32" class="validate-locally" value="<?php echo Input::get('email');?>">
							<span class="errors">
								<?php if(isset($valErrors["email"])){echo implode(", ",$valErrors["email"]);}?>
							</span>
							<span class="no-errors"></span>
							<span class="input-info" id="info"></span>
						</div> 

						<div class="form-field">
							<label for="username">Choose a username</label>
							<input type="text" name="username" id="username" maxlength="32" class="validate-locally" value="<?php echo Input::get('username');?>">
							<span class="errors">
								<?php if(isset($valErrors["username"])){echo implode(", ",$valErrors["username"]);}?>
							</span>
							<span class="no-errors"></span>
							<span class="input-info" id="info"></span>
						</div>

						<div class="form-field">
							<label for="password">Create a password</label>
							<input type="password" name="password" id="password" maxlength="32" class="validate-locally">
							<span class="errors">
								<?php if(isset($valErrors["password"])){echo implode(", ",$valErrors["password"]);}?>
							</span>
							<span class="no-errors"></span>
							<span class="input-info" id="info"></span>
						</div>

						<div class="form-field">
							<label for="confirmpassword">Confirm your password</label>
							<input type="password" name="confirmpassword" id="confirmpassword" maxlength="32" class="validate-locally">
						   <span class="errors"><?php if(isset($valErrors["confirmpassword"])){echo implode(", ",$valErrors["confirmpassword"]);}?></span> 
						   <span class="no-errors"></span>
						</div> 

						<input type="hidden" name="token" value="<?php echo Token::generate();?>">
		                <div class="form-field">
		                	<input type="submit" class="submit" value="Register" >
		                </div> 

		                        <div class="ajax-message"></div>                    <!--end of ajax-message-->
					</fieldset>
				</form>
			</div>
		</div>
		<script src="../js/validate.js">
		</script>
	</body>
</html> 