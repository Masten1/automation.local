<?php

abstract class fvUser extends fvRoot {
	abstract function getLogin();
	
	abstract function getFullName();
	
	abstract function check_acl ($acl_name, $action = 'index');
	
	abstract function isRoot();
}
?>
