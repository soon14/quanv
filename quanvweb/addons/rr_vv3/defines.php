<?php
if (!(defined('IN_IA'))) {
	exit('Access Denied');
}


define('rr_vv3_DEBUG', false);
!(defined('rr_vv3_PATH')) && define('rr_vv3_PATH', IA_ROOT . '/addons/rr_vv3/');
!(defined('rr_vv3_CORE')) && define('rr_vv3_CORE', rr_vv3_PATH . 'core/');
!(defined('rr_vv3_DATA')) && define('rr_vv3_DATA', rr_vv3_PATH . 'data/');
!(defined('rr_vv3_VENDOR')) && define('rr_vv3_VENDOR', rr_vv3_PATH . 'vendor/');
!(defined('rr_vv3_CORE_WEB')) && define('rr_vv3_CORE_WEB', rr_vv3_CORE . 'web/');
!(defined('rr_vv3_CORE_MOBILE')) && define('rr_vv3_CORE_MOBILE', rr_vv3_CORE . 'mobile/');
!(defined('rr_vv3_CORE_SYSTEM')) && define('rr_vv3_CORE_SYSTEM', rr_vv3_CORE . 'system/');
!(defined('rr_vv3_PLUGIN')) && define('rr_vv3_PLUGIN', rr_vv3_PATH . 'plugin/');
!(defined('rr_vv3_PROCESSOR')) && define('rr_vv3_PROCESSOR', rr_vv3_CORE . 'processor/');
!(defined('rr_vv3_INC')) && define('rr_vv3_INC', rr_vv3_CORE . 'inc/');
!(defined('rr_vv3_URL')) && define('rr_vv3_URL', $_W['siteroot'] . 'addons/rr_vv3/');
!(defined('rr_vv3_TASK_URL')) && define('rr_vv3_TASK_URL', $_W['siteroot'] . 'addons/rr_vv3/core/task/');
!(defined('rr_vv3_LOCAL')) && define('rr_vv3_LOCAL', '../addons/rr_vv3/');
!(defined('rr_vv3_STATIC')) && define('rr_vv3_STATIC', rr_vv3_URL . 'static/');
!(defined('rr_vv3_PREFIX')) && define('rr_vv3_PREFIX', 'rr_v_');
define('rr_vv3_PLACEHOLDER', '../addons/rr_vv3/static/images/placeholder.png');

?>