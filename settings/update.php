<?php
use codeshak\classes\Input;
use codeshak\classes\Token; 
use codeshak\classes\Validation;
use codeshak\classes\User;
use codeshak\classes\Hash;
use codeshak\classes\Session;
use codeshak\classes\Redirect;

require_once "../app/init.php";

$user = new User();
if(!$user->LoggedIn()){
	Redirect::to("../index.php");
}else if(Input::exists()){
	if(Token::check(Input::get('token'))){
		$validate  = new Validation();
		if(Input::get("email") !== $user->data()->email){
			$validate  = $validate->check($_POST,array(
				"email" 		  => array(
					"maxLength"   => 32, 
					"unique"      => "users",
					"regex"       => "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i"
				),
				"name" 		  => array(
					"required"    => true,
					"alnumdash"   => "/^[a-z0-9_\-]+$/i"
				) 
			)); 
		}else{
			$validate  = $validate->check($_POST,array(
				"name" 		      => array(
					"required"    => true,
					"min"         => 2,
					"max"         => 50,
					"alnumdash"   => "/^[a-z0-9_\-]+$/i"
				) 
			));
		}
		if($validate->passed()){
		
			//array_walk($_POST,"Sanatize::arraySanatize");
			
			$user      = new User();
			$emailcode = Hash::make(Input::get("email"));

			try{ 
				$user->update(array(
						"email"      => Input::get("email"),
						"emailcode"  => $emailcode,
						"name"       => Input::get("name")
					));
				Redirect::to("update.php?success");
			}catch(Exception $e){
				echo $e->getMessage();
			}
			
		}else{
			$valErrors = $validate->errors();
		}
	}
}

?>


<form action="" class="update profile" method="post" enctype="multipart/form-data">
	<fieldset>
		<legend>Update</legend>
		<div class="form-field">
			<label for="name">Name</label>
			<input type="text" name="name" id="name" maxlength="32" class="validate-locally" value="<?php echo $user->data()->name;?>">
			<span class="errors">
				<?php if(isset($valErrors["name"])){echo implode(", ",$valErrors["name"]);}?>
			</span>
			<span class="no-errors"></span>
			<span class="input-info" id="info"></span>
		</div>
		<div class="form-field">
			<label for="email">Email Address</label>
			<input type="email" name="email" id="email" maxlength="32" class="validate-locally" value="<?php echo $user->data()->email;?>">
			<span class="errors">
				<?php if(isset($valErrors["email"])){echo implode(", ",$valErrors["email"]);}?>
			</span>
			<span class="no-errors"></span>
			<span class="input-info" id="info"></span>	
		</div>
		<input type="submit" value="Update Profile">
		<input type="hidden" name="token" value="<?php echo Token::generate();?>">
	</fieldset>
</form>

