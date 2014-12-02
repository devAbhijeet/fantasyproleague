<?php
function parse($data){
	return htmlentities($data,ENT_QUOTES,'UTF-8');
}