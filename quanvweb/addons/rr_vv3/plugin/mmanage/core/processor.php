<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

require IA_ROOT . '/addons/rr_vv3/defines.php';
require rr_vv3_INC . '/plugin_processor.php';
class MmanageProcessor extends PluginProcessor
{
	public function __construct()
	{
		parent::__construct('mmanage');
	}

	public function respond($obj = NULL)
	{
		global $_W;
		$message = $obj->message;
		$content = $obj->message['content'];
		$msgtype = strtolower($message['msgtype']);
		$event = strtolower($message['event']);
		if (($msgtype == 'text') || ($event == 'click')) {
			$data = m('common')->getPluginset('mmanage');

			if (empty($data)) {
				return $this->responseEmpty();
			}

			if (empty($data['status']) || empty($data['open'])) {
				return $this->responseEmpty();
			}

			$resp = array('title' => $data['title'], 'description' => $data['desc'], 'picurl' => tomedia($data['thumb']), 'url' => mobileUrl('mmanage', array(), true));
			return $obj->respNews($resp);
		}

		return $this->responseEmpty();
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
