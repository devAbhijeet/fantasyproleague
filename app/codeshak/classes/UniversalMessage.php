<?php namespace codeshak\classes;

class UniversalMessage{

	private $_message = null;

	public function __construct(){
		$num_args = func_num_args();

		if($num_args === 1){
			$this->_message = "
				Hello ".func_get_arg(0).", It seems like you have lost your username <br><br>
 				In response to which we have recovered it for you.Your Username is - ".func_get_arg(0)."<br><br>
 				Please click on link below to login with new Username<br><br>
				<a href='http://localhost/fantasyproleague/login/login.php'>Login</a>  --CodeShak
			";
		}else if($num_args === 2){
			$this->_message = "
				Hello ".func_get_arg(0).", It seems like you have lost your password <br><br>
 				In response to which we have generated new one for you.Your new Password is - ".func_get_arg(1)."<br><br>
 				Please click on link below to login with new Password<br><br>
				<a href='http://localhost/fantasyproleague/login/login.php'>Login</a>  --CodeShak
			";
		}else{
			$this->_message = "";
		}

		return $this;
	}

	public function message(){
		return $this->_message;
	}
}
