<?php
if (!defined('IN_IA')) {
	exit('Access Denied');
}

class Task_RrvV3Page extends WebPage
{
	public function main()
	{
		$this->runTasks();
	}
}

?>
