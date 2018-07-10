<?php

require_once '../vendor/autoload.php';
use WindowsAzure\Common\ServicesBuilder;
use MicrosoftAzure\Storage\Common\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\CreateBlobOptions;

function connect_blob(){
$key = "agvaqI3CpuNhH2DYYITzR7ve1IG6NnVIUa5Vio47vO3HEwdU0bFjEynXLRvBYLzmuTagJjo8A6otW9Wi2ECK7w==";
$account = "xyzcs";
	$connectionString = "DefaultEndpointsProtocol=https;AccountName=$account;AccountKey=$key";
	$blobRestProxy = ServicesBuilder::getInstance()->createBlobService($connectionString);
	
	return $blobRestProxy;
}

include("globalLocal.php");
	
function getdblocalblob(){
	global $hostnamelocal,$dbnamelocal,$usernamelocal,$passwordlocal;
    $pdo = new PDO("mysql:host=" . $hostnamelocal . ";dbname=".$dbnamelocal ,
        $usernamelocal, $passwordlocal,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") ); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function uploadBlob($filesrc){
	global $ProductPhotoFilepath;
	if (isset($_SERVER['HTTPS']) &&
	    ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
	    isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
	    $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
	  $protocol = 'https://';
	}
	else {
	  $protocol = 'http://';
	}
	$portnumber=$_SERVER['SERVER_PORT'];
	$file = $filesrc['file'];
	$name= $file['name'];
	$tmp_name = $file['tmp_name'];
	$tmp_file_path = $ProductPhotoFilepath.$name;
	$arraytoresponse=array();	
	if(move_uploaded_file($tmp_name,$tmp_file_path)){
		
		$link=str_replace($_SERVER['DOCUMENT_ROOT'], "", $ProductPhotoFilepath);
		if($portnumber=="80")
			$linktoresponse=$protocol.$_SERVER['SERVER_NAME'].$link.$name;
		else
			$linktoresponse=$protocol.$_SERVER['SERVER_NAME'].":$portnumber".$link.$name;
		$arraytoresponse['photo']=$linktoresponse;	
		echo json_encode($arraytoresponse);
	}else{
		$arraytoresponse['photo']="https://cdn1.iconfinder.com/data/icons/media-devices-outline/100/cloud_error-01-512.png";	
		echo json_encode($arraytoresponse);
	}
	

	
	
}


?>