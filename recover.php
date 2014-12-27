<?php
use codeshak\classes\Input;
use codeshak\classes\Token; 
use codeshak\classes\Validation;
use codeshak\classes\User;
use codeshak\classes\Hash;
use codeshak\classes\Mail;
use codeshak\classes\Redirect;
use codeshak\classes\UniversalMessage as UniMess; 

use codeshak\generals\Sanatize;

require_once "app/init.php";

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
				//array_walk($_POST,"Sanatize::arraySanatize");
			
				$user = new User();
				$user->find(Input::get("email"),"email");

				if($user){
					if(Input::get("mode") == "username"){

						$uniMess = new UniMess($user->data()->username);
						$body = $uniMess->message();  

						$details = [
							"name"     => $user->data()->username,
 							"email"    => Input::get("email"),
 							"subject"  => "Username Recovery", 
							"body"     => $body 
						];

						if(Mail::getMailHandle()->setDetails()->sendMail($details)->passed()){
							Redirect::to("index.php");
						} 
					}else if(Input::get("mode") == "password"){
						$salt    = Hash::salt(32);
						$pass    = rand(999,999999);
						$newPass = substr(Hash::make($pass,$salt),0,8);

						$user->update(array(
								"password"                 => $newPass,
								"salt"                     => $salt,
								"password_recover"         => "1"
							),$user->data()->id);

						$uniMess = new UniMess($user->data()->username,$pass);
						$body = $uniMess->message();

						$details = [
							"name"     => $user->data()->username,
 							"email"    => Input::get("email"),
 							"subject"  => "Password Recovery", 
							"body"     => $body
						];

						if(Mail::getMailHandle()->setDetails()->sendMail($details)->passed()){
							Redirect::to("index.php");
						}
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