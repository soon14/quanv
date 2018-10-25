<?php

if (!defined('IN_IA')) {
	exit('Access Denied');
}

require rr_vv3_PLUGIN . 'app/core/page_mobile.php';
require 'cos-sdk-v5/cos-autoloader.php';
require 'qcloudapi-sdk-php/src/QcloudApi/QcloudApi.php';
require 'src/Vod/VodApi.php';
require 'src/Vod/Conf.php';

use Vod\VodApi;
class Uploadvideo_RrvV3Page extends AppMobilePage
{
	public function upload()
	{
		global $_W;
		global $_GPC;
		load()->func('file');
		$field = $_GPC['file'];

		VodApi::initConf("AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ", "61iFXbR2SdaMvFLCoCLFvNacQzKSM2Ui");//全V
		// VodApi::initConf("AKIDRrMgoo7Ko6FCycaNxfFhwziJCvTpM11N", "NdhHYn9PVjp4DPRKI4MYebEwSq8GQODq");//测试

		if (!empty($_FILES[$field]['name'])) {
			if (is_array($_FILES[$field]['name'])) {
				$files = array();

				foreach ($_FILES[$field]['name'] as $key => $name) {
					$file = array('name' => $name, 'type' => $_FILES[$field]['type'][$key], 'tmp_name' => $_FILES[$field]['tmp_name'][$key], 'error' => $_FILES[$field]['error'][$key], 'size' => $_FILES[$field]['size'][$key]);
					$result = $this->uploadFile($file);

					if ($result['status'] == 'error') {
						$result = array('status' => 0);
					}
					else {
						$result = array('status' => 1, 'filename' => $result['path'], 'url' => tomedia($result['filename']));
					}

					$files[] = $result;
				}
			}
			else {
				$result = $this->uploadFile($_FILES[$field]);

				if ($result['status'] == 'error') {
					app_error(AppError::$UploadFail, $result['message']);
				}

				$files = array(
					array('status' => 1, 'url' => tomedia($result['filename']), 'filename' => $result['filename'])
					);
			}

			$result = $this->uploadFile($_FILES[$field]);

			$data = VodApi::upload(
				array (
					'videoPath' => ATTACHMENT_ROOT.$result['filename'],//视频路径
					// 'coverPath' => ATTACHMENT_ROOT.'videos/' .$_W['uniacid']. '/'. date('Y/m/').'/123.jpg',//视频封面
				    // 'videoPath' => $_GPC['videoPath'],//视频路径
	        		// 'coverPath' => '/data/videos/Wildlife.jpg',//视频封面
				),
    			array (
			        'procedure' => 'QCVB_SimpleProcessFile({10,20},150,10,10)'
			    )
			);
			if(!empty($data)){
				unlink(ATTACHMENT_ROOT.$result['filename']);
				// unlink(ATTACHMENT_ROOT.'videos/' .$_W['uniacid']. '/'. date('Y/m/').'/123.jpg');
			}
			
			app_json($data);
		}
		else {
			app_error(AppError::$UploadNoFile, '未选择视频');
		}
	}

	protected function uploadFile($uploadfile)
	{
		global $_W;
		global $_GPC;
		$result['status'] = 'error';

		if ($uploadfile['error'] != 0) {
			$result['message'] = '上传失败';
			return $result;
		}

		load()->func('file');

		$_W['uploadsetting'] = array();
		$_W['uploadsetting']['video']['folder'] = $path;
		$_W['uploadsetting']['video']['extentions'] = $_W['config']['upload']['video']['extentions'];
		$_W['uploadsetting']['video']['limit'] = $_W['config']['upload']['video']['limit'];
		$file = file_upload($uploadfile, 'video');

		if (is_error($file)) {
			$ext = pathinfo($uploadfile['name'], PATHINFO_EXTENSION);
			$ext = strtolower($ext);
			$result['message'] = $file['message'] . ' 扩展名: ' . $ext . ' 文件名: ' . $uploadfile['name'];
			return $result;
		}

		if (function_exists('file_remote_upload')) {
			$remote = file_remote_upload($file['path']);

			if (is_error($remote)) {
				$result['message'] = $remote['message'];
				return $result;
			}
		}

		$result['status'] = 'success';
		$result['url'] = $file['url'];
		$result['error'] = 0;
		$result['filename'] = $file['path'];
		$result['url'] = tomedia($result['filename']);
		return $result;
	}

	public function remove()
	{
		global $_W;
		global $_GPC;
		load()->func('file');
		$file = $_GPC['file'];
		file_delete($file);
		app_json();
	}
}

?>
