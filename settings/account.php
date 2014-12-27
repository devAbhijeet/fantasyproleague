<?php
use codeshak\classes\Input;
use codeshak\classes\Token; 
use codeshak\classes\Validation;
use codeshak\classes\User;
use codeshak\classes\Hash;
use codeshak\classes\Redirect;

require_once "../app/init.php";

$user = new User();

if(!$user->LoggedIn()){
	Redirect::to("../index.php");
}

if(Input::exists()){
	if(Token::check(Input::get("token"))){
		$validate = new Validation();
		$validate = $validate->check($_POST,array(
			"password"            => array(
					"required"    => true,
					"min"         => 6
				),
			"newpassword"         => array(
					"required"    => true,
					"min"         => 6
				),
			"confirm-newpassword" => array(
					"required"    => true,
					"min"         => 6,
					"matches"     => "newpassword"
				)
			));
		if($validate->passed()){
			if($user->data()->password_recover){
				if(substr(Hash::make(Input::get("password"),$user->data()->salt),0,8) !== $user->data()->password){
					echo "Current password is incorrect";
				}else{
					 $salt = Hash::salt(32);
				 	 $user->update(array(
				 		"password"         => Hash::make(Input::get("newpassword"),$salt),
				 		"salt"     		   => $salt,
				 		"password_recover" => "0"
				 	 ));
				 	Redirect::to("account.php?success"); 
				}
			}else{
				if(Hash::make(Input::get("password"),$user->data()->salt) !== $user->data()->password){
					echo "Current password is incorrect";
				}else{
					 $salt = Hash::salt(32);
				 	 $user->update(array(
				 		"password"         => Hash::make(Input::get("newpassword"),$salt),
				 		"salt"     		   => $salt,
				 		"password_recover" => "0"
				 	 ));
				 	Redirect::to("account.php?success"); 
				}	
			}
		}else{
			$valErrors = $validate->errors();
		}
	}
}


?>






<form action="" method="post" name="form-account-update" class="form-account-update" id="form-update-account" novalidate >
					<fieldset>
					<legend>Account Update</legend> 
						<div class="form-field">
							<label for="password">Current password</label>
							<input type="password" name="password" id="password" maxlength="32" class="validate-locally">
							<span class="errors">
								<?php if(isset($valErrors["password"])){echo implode(", ",$valErrors["password"]);}?>
							</span>
							<span class="no-errors"></span>
							<span class="input-info" id="info"></span>
						</div>

						<div class="form-field">
							<label for="newpassword">New your password</label>
							<input type="password" name="newpassword" id="newpassword" maxlength="32" class="validate-locally">
						   <span class="errors"><?php if(isset($valErrors["newpassword"])){echo implode(", ",$valErrors["newpassword"]);}?></span> 
						   <span class="no-errors"></span>
						</div>

						<div class="form-field">
							<label for="confirm-newpassword">Confirm New password</label>
							<input type="password" name="confirm-newpassword" id="confirm-newpassword" maxlength="32" class="validate-locally">
						   <span class="errors"><?php if(isset($valErrors["confirm-newpassword"])){echo implode(", ",$valErrors["confirm-newpassword"]);}?></span> 
						   <span class="no-errors"></span>
						</div> 

						<input type="hidden" name="token" value="<?php echo Token::generate();?>">
		                <div class="form-field">
		                	<input type="submit" class="submit" value="Register" >
		                </div> 

		                        <div class="ajax-message"></div>                    <!--end of ajax-message-->
					</fieldset>
				</form>