<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require_once IA_ROOT . '/addons/rr_vv3/version.php';
require_once IA_ROOT . '/addons/rr_vv3/defines.php';
require_once rr_vv3_INC . 'functions.php';
class rr_vv3Module extends WeModule
{
	public function welcomeDisplay()
	{
		header('location: ' . webUrl());
		exit();
	}
}


?>