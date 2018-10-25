<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require_once IA_ROOT . '/addons/rr_vv3/version.php';
require_once IA_ROOT . '/addons/rr_vv3/defines.php';
require_once rr_vv3_INC . 'functions.php';
require_once rr_vv3_INC . 'receiver.php';
class rr_vv3ModuleReceiver extends Receiver
{
	public function receive()
	{
		parent::receive();
	}
}


?>