<?php
class User{
	private $_db = null;

	public function __construct($user = null){
		$this->_db = DB::getInstance();
	}

	public function create($fields = array()){
		if(!$this->_db->insert("users",$fields)){
			throw new Exception("Error Processing Request", 1);
		}
	}
}