<?php
require_once "core/init.php";
dir_name_autoload("fantasyproleague");

$modes = ["username","password"];
if(Input::exists("get") && in_array(Input::get("mode"), $modes)){
	if(Input::exists()){
	 	if(Token::check(Input::get("token"))){
			$validate = new Validation();
			$validate = $validate->check($_POST,[
					"email" => [
						"maxLength"   => 32,
						"regex"       => "/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.(?:[A-Z]{2}|com|org|net|edu|gov|mil|biz|info|mobi|name|aero|asia|jobs|museum)$/i" 
				 	] 
			]);

			if($validate->passed()){
				array_walk($_POST,"Sanatize::arraySanatize");
			
				$user = new User(); 
				if($user->find(Input::get("email"),"email")){
					if(Input::get("mode") == "username"){
						$body = "Hello ".$user->data()->username."It seems like you have lost your username <br><br>
								In response to which we have recovered it for you.Your Username is -".$user->data()->username." <br><br>
								Please click on link below to login with new Username<br><br>
								<a href='http://localhost/fantasyproleague/login/login.php'>Login</a>  --CodeShak
								"; 

						$details = [
							"name"     => $user->data()->username,
 							"email"    => Input::get("email"),
 							"subject"  => "Email Activation", 
							"body"     => $body 
						];

						if(Mail::getMailHandle()->setDetails()->sendMail($details)->passed()){
							Redirect::to("recover.php?success");
						}
					}else{
						// password
					}
				}else{
					$valErrors = ["email"=>["Email does'nt exists"]];
				} 
			}else{
				$valErrors = $validate->errors();
			}
		}
	}
}else{
	Redirect::to(404); 
}



?>






<form action="" method="post">
	<div class="form-field">
		<label for="email">Email Address</label>
		<input type="email" name="email" id="email" maxlength="32" class="validate-locally" value="<?php echo Input::get('email');?>">
		<span class="errors">
			<?php if(isset($valErrors["email"])){echo implode(", ",$valErrors["email"]);}?>
		</span>
	</div>

	<input type="hidden" name="token" value="<?php echo Token::generate();?>">
	<input type="submit" value="RECOVER">
</form>