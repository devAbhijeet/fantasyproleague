<?php
require_once "core/init.php";
dir_name_autoload('fantasyproleague'); 

$user = new User();
$mail = new Mail(new PHPMailer(),array(
			"name"     => "name",
			"email"    => "name@gmail.com",
			"subject"  => "Test Email",
			"body"     => "This is a test email"
		));

if($user->loggedIn()):?>
 
<p>
	Hello,<?php echo $user->data()->username;?>
</p>

<a href="login/logout.php">Logout</a>

<?php else:?>
<p>
	<a href="login/login.php">Login</a> <br> 
	<a href="signup/register.php">Register</a>
</p>

<?php endif;?>