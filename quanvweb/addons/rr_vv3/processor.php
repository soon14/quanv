<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


require_once IA_ROOT . '/addons/rr_vv3/version.php';
require_once IA_ROOT . '/addons/rr_vv3/defines.php';
require_once rr_vv3_INC . 'functions.php';
require_once rr_vv3_INC . 'processor.php';
require_once rr_vv3_INC . 'plugin_model.php';
require_once rr_vv3_INC . 'com_model.php';
class rr_vv3ModuleProcessor extends Processor
{
	public function respond()
	{
		return parent::respond();
	}
}


?>