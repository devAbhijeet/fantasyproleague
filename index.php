<?php

use codeshak\classes\User as User;
use codeshak\classes\Redirect as Redirect; 

require_once 'app/init.php';

$user    = new User();
$current = end(explode("/",$_SERVER["SCRIPT_NAME"]));

if($user->loggedIn()):?>
	
	<?php 
		if($current !== "account.php" && $user->data()->password_recover == 1){
			Redirect::to("settings/account.php");
		}
	?>

<p>
	Hello,<?php echo $user->data()->username;?>
</p>

<ul>
	<li><a href="login/logout.php">Logout</a></li>
	<li><a href="settings/update.php">Update Settings</a></li>
	<li><a href="settings/account.php">Account Settings</a></li>
</ul>

<?php else:?>
<p>

<ul>
	<li><a href="login/login.php">Login</a></li>
	<li><a href="signup/register.php">Register</a></li>
</ul>

</p>
<?php endif;?>