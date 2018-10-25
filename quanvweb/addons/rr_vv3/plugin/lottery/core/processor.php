<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require IA_ROOT . '/addons/rr_vv3/defines.php';
require rr_vv3_INC . 'plugin_processor.php';
class LotteryProcessor extends PluginProcessor
{
	public function __construct()
	{
		parent::__construct('lottery');
	}

	public function respond($obj = NULL)
	{
		global $_W;
		$message = $obj->message;
		$msgtype = strtolower($message['msgtype']);
		$event = strtolower($message['event']);
	}

	private function responseEmpty()
	{
		ob_clean();
		ob_start();
		echo '';
		ob_flush();
		ob_end_flush();
		exit(0);
	}
}

?>
