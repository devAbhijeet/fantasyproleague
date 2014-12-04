<?php
class User{
	private $_db = null,
			$_loggedIn = false,
			$_data,
			$_sessionName,
			$cookieName;

	public function __construct($user = null){
		$this->_db = DB::getInstance();
		$this->_sessionName = Config::get("session/session_name");

		if(!$user){
			if(Session::exists($this->_sessionName)){
				$user = Session::get($this->_sessionName);
				if($this->find($user)){
					$this->_loggedIn = true;
				}
			}
		}else{
			$this->find($user);
		}

	}

	public function create($fields = array()){
		if(!$this->_db->insert("users",$fields)){
			throw new Exception("Error Processing Request", 1);
		}
	}

	public function find($user = null){
		$field = is_numeric($user) ? "id" : "username";
		
		if($user){
			$data = $this->_db->get("users",array($field,"=",$user));
			if($data->count()){
				$this->_data = $data->first();
				return true;
			}
		}
		return false;		
	}

	public function login($username=null,$password=null){
		$user = $this->find($username);
		if($user){
			if($this->data()->password == Hash::make($password,$this->data()->salt)){
				Session::put($this->_sessionName,$this->data()->id);
				return true;
			}
		}
		return false;
	}

	public function data(){
		return $this->_data;
	}

	public function loggedIn(){
		return $this->_loggedIn;
	}

	public function logout(){
		Session::delete($this->_sessionName);
 	}
}