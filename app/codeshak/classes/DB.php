<?php namespace codeshak\classes;

use PDO;

class DB{

	private static $_instance = null;
	private $_pdo,
			$_query,
			$_result,
			$_error = false,
			$_count = 0;

    private function __construct(){
    	try{
    		$this->_pdo = new PDO("mysql:host=".Config::get('mysql/host').";dbname=".Config::get('mysql/db'),
    			Config::get('mysql/username'),
    			Config::get('mysql/password'));
    	}catch(PDOException $e){
    		$e->getMessage();
    	}
    }

    public static function getInstance(){
    	if(!isset(self::$_instance)){
    		self::$_instance = new DB();
    	}
    	return self::$_instance; 
    }

    public function query($query,$param=array()){
    	$this->_error = false;
    	if($this->_query = $this->_pdo->prepare($query)){
    		if(count($param)){
    			$x = 1;
    			foreach ($param as $value) {
    				$this->_query->bindValue($x,$value);
    				$x++;
    			}
    		}
			if($this->_query->execute()){
				$this->_result = $this->_query->fetchAll(PDO::FETCH_OBJ);
				$this->_count  = $this->_query->rowCount();
			}else{
				$this->_error = true;
			}
    	}
    	return $this;  
    }

    public function action($action,$table,$param = array()){
    	if(count($param)>=3){
    		$operators = ["<",">","<=",">=","!=","="];
    		$field     = $param[0];
    		$operator  = $param[1];
    		$value     = $param[2]; 	
    	
	    	if(in_array($operator,$operators)){
	    		$sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?";
	    		if(!$this->query($sql,array($value))->error()){
	    			return $this;
	    		}
	    	}
	    }
    	return false;
    }

    public function insert($table,$param = array()){
    	if(count($param)){
    		$values = "";
    		$x = 1;
    		$keys = array_keys($param);

    		foreach ($param as $value) {
    			$values.= "?";
    			if($x<count($param)){
    				$values.=", ";
    			}
    			$x++;
    		}

    		$sql = "INSERT INTO {$table} (`".implode('`, `',$keys)."`) VALUES ({$values})";
    		if(!$this->query($sql,$param)->error()){
    			return true;
    		}  
    	}
    	return false;
    }

    public function update($table,$id,$param = array()){
    	if(count($param)){
    		$set = "";
    		$x   = 1;

    		foreach ($param as $field => $value) {
    			$set.= "{$field} = ?";
    			if($x<count($param)){
    				$set.=", ";
    			}
    			$x++;
    		} 

    		$sql = "UPDATE {$table} SET {$set} WHERE id = {$id}";
    		if(!$this->query($sql,$param)->error()){
    			return true;
    		}
    	}
    	return false;
    }

    public function aoUpdate($table,$operator,$fields = array(),$param = array()){
        if(count($param) && count($fields)){
            $set = ""; $at  = "";
            $x   = 1; $y    = 1;
            foreach ($param as $field => $value) {
                $set.= "{$field} = ?";
                if($x<count($param)){
                    $set.=", ";
                }
                $x++;
            }

            foreach ($fields as $field => $value) { 
                if(is_numeric($value)){
                    $at.= "{$field} = {$value}";
                }else{
                    $at.= "{$field} = '".$value."'";
                }
                if($y<count($fields)){
                    $at.=" {$operator} ";
                }
                $y++;
            }

            $sql = "UPDATE {$table} SET {$set} WHERE {$at}";
            
            if(!$this->query($sql,$param)->error()){
                return true;
            }
        }
        return false;
    }  

    public function get($table,$param = array()){
    	return $this->action("SELECT *",$table,$param);
    }

    public function delete($table,$param){
    	return $this->action("DELETE",$table,$param);
    }

    public function result(){
    	return $this->_result;
    }

    public function first(){
    	return $this->result()[0];
    }
    public function count(){
    	return $this->_count;
    }

    public function error(){
    	return $this->_error;
    } 
}