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
class Testvideo_RrvV3Page extends AppMobilePage
{
	// public function get_data()
	// {
	// 	global $_W;
	// 	global $_GPC;
	// 	$uniacid = $_W['uniacid'];

	// 	load()->func('communication');

	// 	//1、对参数排序
	// 	$data = array(
	// 		'Action'				=> 	'GetVideoInfo',
	// 		'SecretId'				=>	'AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ',
	// 		'Timestamp'				=>	time(),
	// 		'Region'				=>	'ap-guangzhou',
	// 		'Nonce'					=>	random(6, true),
	// 		'SignatureMethod'		=>	'HmacSHA256',
	// 		'InstanceIds.0'			=>	'ins-7447398154966722659',
	// 	);

	// 	ksort($data);

	// 	//2、拼接请求字符串
	// 	foreach($data as $key=>$val){

	// 		$str .= $key.'='.$val.'&';

	//     }

	//     //3、拼接签名原文字符串
	//     $srcStr = 'GETvod.api.qcloud.com/v2/index.php?'.rtrim($str,'&');

	//     //4、 生成签名串
	// 	$secretKey = '61iFXbR2SdaMvFLCoCLFvNacQzKSM2Ui';
	// 	$signStr = base64_encode(hash_hmac('sha256', $srcStr, $secretKey, true));
	// 	//5、URL编码
	// 	$mysignature = urlencode($signStr);

	// 	//6、发请求
	// 	$url = 'https://vod.api.qcloud.com/v2/index.php?'.rtrim($str,'&').'&fileId=7447398154966722659&Signature='.$mysignature;
	// 	// $url = 'https://vod.api.qcloud.com/v2/index.php?Action=GetVideoInfo&fileId=7447398154966722659&infoFilter.0=basicInfo&infoFilter.1=transcodeInfo&SecretId=AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ&Timestamp='.time().'&Nonce='.random(6, true).'&Signature='.$mysignature;
		
	// 	$res = ihttp_get($url);

	// 	$result = @json_decode($res['content'], true);

	// 	app_json(array('result' => $result));

	// }

	// public function get_data()
	// {
	// 	global $_W;
	// 	global $_GPC;
	// 	$uniacid = $_W['uniacid'];

	// 	load()->func('communication');

	// 	//1、对参数排序
	// 	$data = array(
	// 		'Action'				=> 	'GetVideoInfo',
	// 		'Nonce'					=>	random(5, true),
 //    		'Region'				=>	'ap-guangzhou',
	// 		'SecretId'				=>	'AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ',
	// 		'Timestamp'				=>	time(),
	// 		'SignatureMethod'		=>	'HmacSHA256',
	// 		'FileId'				=>	'7447398154966722659',
	// 	);

	// 	ksort($data);

	// 	//2、拼接请求字符串
	// 	foreach($data as $key=>$val){

	// 		$str .= $key.'='.$val.'&';

	//     }

	//     //3、拼接签名原文字符串
	//     $srcStr = 'GETvod.api.qcloud.com/v2/index.php?'.rtrim($str,'&');

	//     //4、 生成签名串
	// 	$secretKey = '61iFXbR2SdaMvFLCoCLFvNacQzKSM2Ui';
	// 	$signStr = base64_encode(hash_hmac('sha256', $srcStr, $secretKey, true));
	// 	//5、URL编码
	// 	$mysignature = urlencode($signStr);


	// 	//6、发请求
	// 	// $url = 'https://vod.api.qcloud.com/v2/index.php?'.rtrim($str,'&').'&fileId=7447398154966722659&Signature='.$mysignature;
	// 	$url = 'https://vod.api.qcloud.com/v2/index.php?Action=GetVideoInfo&SecretId=AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ&Region=gz&Timestamp='.time().'&Nonce='.random(5, true).'&instanceId=101&Signature='.$mysignature.'&fileId=7447398154966722659&infoFilter.0=basicInfo&infoFilter.1=transcodeInfo';
		
	// 	$res = ihttp_request($url);

	// 	$result = @json_decode($res['content'], true);

	// 	$result['srcStr'] = $srcStr;
	// 	$result['signStr'] = $signStr;
	// 	$result['mysignature'] = $mysignature;
	// 	$result['url'] = $url;

	// 	app_json(array('result' => $result));

	// }

	public function get_data()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];

		load()->func('communication');

		$fileId = trim($param['fileid']);
		$getParams = array('fileId' => $fileId, 'infoFilter.0' => 'basicInfo', 'infoFilter.1' => 'transcodeInfo', 'infoFilter.2' => 'snapshotByTimeOffsetInfo', 'infoFilter.3' => 'sampleSnapshotInfo');
		$resultArray = videoApi('GetVideoInfo', $getParams);


		app_json(array('result' => $resultArray));

	}

	public function set_image()
	{
		global $_W;
		global $_GPC;
		$param = $_GPC['param'];

		load()->func('communication');

		$fileId = trim($param['fileid']);
		$getParams = array('fileId' => $fileId, 'definition' => 10, 'timeOffset.0' => 10000);
		$resultArray = videoApi('CreateSnapshotByTimeOffset', $getParams);


		app_json(array('result' => $resultArray));

	}

	public function upload()
	{
		global $_W;
		global $_GPC;
		load()->func('file');
		$field = $_GPC['file'];

		VodApi::initConf("AKIDwiIdkk37ZmjQr1Q1GH5f4HG1f8NSqxNZ", "61iFXbR2SdaMvFLCoCLFvNacQzKSM2Ui");//全V
		// VodApi::initConf("AKIDRrMgoo7Ko6FCycaNxfFhwziJCvTpM11N", "NdhHYn9PVjp4DPRKI4MYebEwSq8GQODq");//测试

		if (!empty($_FILES[$field]['name'])) {

			$result = $this->uploadFile($_FILES[$field]);

			$data = VodApi::upload(
				array (
					'videoPath' => ATTACHMENT_ROOT.$result['filename'],//视频路径
				),
    			array (
			        'procedure' => 'QCVB_SimpleProcessFile({20,30},150,10,10)',//视频转码
			    )
			);
			if(!empty($data)){
				unlink(ATTACHMENT_ROOT.$result['filename']);
			}
			
			app_json(array('result' => $data));
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


}

?>
