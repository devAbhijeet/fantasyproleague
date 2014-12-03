<?php
class Validation{
	private $_db = null,
			$_error  = array(),
			$_passes = false,
			$_rules  = array("required","minLength","maxLength","email","regex","unique","matches","alnum");

	public $messages = array(
		       "required"   => "The :field field is required",
		       "minLength"  => "The :field field must of minimum of :rule_value length",
		       "maxLength"  => "The :field field must of maximum of :rule_value length",
		       "email"      => "The :field field is invalid",
		       "regex"      => "The :field field is invalid",
		       "unique"     => "The :field field already exists",
		       "matches"    => "The :field field must match :rule_value",
		       "alnum"      => "The :field field must be alphanumeric "
		   ); 

	public function __construct(){
		$this->_db = DB::getInstance();
	}

	public function check($source,$rules=array()){
		foreach ($source as $field => $value) {
			if(in_array($field, array_keys($rules))){
				$this->validate($source,array(
					"field" => $field,
					"value" => $value,
					"rules" => $rules[$field]
				));
			}
		}
		if(empty($this->_error)){
			$this->_passes = true;
		}

		return $this;
	}

	protected function validate($source,$data=array()){
		$field = $data["field"];
		$value = $data["value"]; 
		foreach ($data["rules"] as $rule => $rule_value){
			if(in_array($rule,$this->_rules)){
				if(!call_user_func_array(array($this,$rule),array($field,$value,$rule_value,$source))){
					$this->addError(str_replace(array(":field",":rule_value"),array($field,$rule_value),$this->messages[$rule]));
				}
			}
		}
	}

	protected function required($field,$value,$rule_value,$source){
		$val = trim($value);
		return !empty($val);
	}

	protected function minLength($field,$value,$rule_value,$source){
		return strlen(trim($value))>=$rule_value;
	}

	protected function maxLength($field,$value,$rule_value,$source){
		return strlen(trim($value))<=$rule_value;
	}

	protected function email($field,$value,$rule_value,$source){
		return filter_var($value,FILTER_VALIDATE_EMAIL);
	}

	protected function unique($field,$value,$rule_value,$source){
		$query = $this->_db->get($rule_value,array("username","=",$value));
		if(!$query->count()){
			return true; 
		}
		return false; 
	}

	protected function matches($field,$value,$rule_value,$source){
		return $value == $source[$rule_value];  
	}

	protected function alnum($field,$value,$rule_value,$source){
		return ctype_alnum($value);
	}

	protected function regex($field,$value,$rule_value,$source){
		return preg_match($rule_value,$value) !==1 ? true : false;
	}

	public function passed(){
		return $this->_passes;
	}

	public function addError($error){
		$this->_error[] = $error;
	}

	public function errors(){
		return $this->_error;
	}
	public function hasError(){
		return !empty($this->_error);
	}

}