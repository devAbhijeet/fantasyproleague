<?php
require_once "core/init.php";
dir_name_autoload('fantasyproleague');

if(Session::exists("success")){
	echo Session::flash("success");
}
