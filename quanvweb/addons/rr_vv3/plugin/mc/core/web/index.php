<?php
if (!defined("IN_IA")) {
	exit("Access Denied");
}

class Index_RrvV3Page extends PluginWebPage
{
	public function main()
	{
		header("location: " . webUrl("mc/index/index"));
		exit();
	}

	public function index()
	{
		global $_W;
		include $this->template();
	}
}


?>