<?php
require_once "core/init.php";
dir_name_autoload('fantasyproleague'); 

$user = new User();
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