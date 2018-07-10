<?php
/*
{ "expiration": "2015-12-30T12:00:00.000Z",
  "conditions": [
    {"bucket": "sigv4examplebucket"},
    ["starts-with", "$key", "user/user1/"],
    {"acl": "public-read"},
    {"success_action_redirect": "http://sigv4examplebucket.s3.amazonaws.com/successful_upload.html"},
    ["starts-with", "$Content-Type", "image/"],
    {"x-amz-meta-uuid": "14365123651274"},
    {"x-amz-server-side-encryption": "AES256"},
    ["starts-with", "$x-amz-meta-tag", ""],

    {"x-amz-credential": "AKIAIOSFODNN7EXAMPLE/20151229/us-east-1/s3/aws4_request"},
    {"x-amz-algorithm": "AWS4-HMAC-SHA256"},
    {"x-amz-date": "20151229T000000Z" }
  ]
}
*/
function s3SignitureCredentials($currentDate,$currentDate2){
	global $bucketname,$s3key,$s3region;

	$signiture = array();
	$conditions = array();
	$conditions2 = array();
	$conditions3 = array();
	$conditions4 = array();
	$conditions5 = array();
	$conditions6 = array();
	$conditions7 = array();
	$conditions8 = array();
	$conditions9 = array();
	$array1 = array();
	$array2 = array();
	$array3 = array();


	$conditions2['bucket'] = $bucketname;
	array_push($conditions,$conditions2);

	array_push($array1,'starts-with');
	array_push($array1,'$key');
	array_push($array1,'Temp/');
	array_push($conditions,$array1);

	$conditions3['acl'] = 'public-read';
	array_push($conditions,$conditions3);

	$conditions4['success_action_redirect'] = setRedirectLink()."/api/public/Redirect/";
	array_push($conditions,$conditions4);

	array_push($array2,'starts-with');
	array_push($array2,'$Content-Type');
	array_push($array2,'image/');
	array_push($conditions,$array2);

	$conditions5['x-amz-meta-uuid'] = '14365123651274';
	array_push($conditions,$conditions5);

	$conditions6['x-amz-server-side-encryption'] = 'AES256';
	array_push($conditions,$conditions6);

	array_push($array3,'starts-with');
	array_push($array3,'$x-amz-meta-tag');
	array_push($array3,'');
	array_push($conditions,$array3);

	$conditions7['x-amz-credential'] = $s3key.'/'.$currentDate2.'/'.$s3region.'/s3/aws4_request';	
	array_push($conditions,$conditions7);

	$conditions8['x-amz-algorithm'] = "AWS4-HMAC-SHA256";
	array_push($conditions,$conditions8);

	$conditions9['x-amz-date'] = $currentDate2.'T000000Z';
	array_push($conditions,$conditions9);

	$signiture['expiration'] = $currentDate."T12:00:00.000Z";
	$signiture['conditions'] = $conditions;

	return json_encode($signiture);
}

function base64Signiture($currentDate,$currentDate2){
 return base64_encode(str_replace('\\','',s3SignitureCredentials($currentDate,$currentDate2)));
}

function setRedirectLink(){
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

	if($portnumber=="80")
		$linktoresponse=$protocol.$_SERVER['SERVER_NAME'];
	else
		$linktoresponse=$protocol.$_SERVER['SERVER_NAME'].":$portnumber";

	return $linktoresponse;
}

function s3Signiture($currentDate,$currentDate2){
	global $bucketname,$s3key,$s3region,$s3secret;

	$secretkey=$s3secret;
	$date=$currentDate2;
	$regionName=$s3region;

	$kDate = hash_hmac('sha256', $date, "AWS4".$secretkey);
	
	$kRegion = hash_hmac('sha256', $regionName,hex2bin($kDate));
	
	$kService = hash_hmac('sha256', "s3", hex2bin($kRegion));
	
	$kSignin = hash_hmac('sha256', "aws4_request", hex2bin($kService));
	
	$signature = hash_hmac('sha256', base64Signiture($currentDate,$currentDate2), hex2bin($kSignin));
	return $signature;
}

function s3Policy(){
	global $bucketname,$s3key,$s3region;

	$now = new DateTime();
	$currentDate = $now->format('Y-m-d');
	$currentDate2 = str_replace('-','',date('Y-m-d',strtotime($currentDate)));

	// echo s3Signiture($currentDate,$currentDate2);
	// echo base64Signiture($currentDate,$currentDate2);

	$final_array = array();

	$final_array['key'] = 'Temp/';	
	$final_array['acl'] = 'public-read';	
	$final_array['success_action_redirect'] = setRedirectLink()."/api/public/Redirect/";
	$final_array['Content-Type'] = "image/jpeg";	
	$final_array['x-amz-meta-uuid'] = "14365123651274";	
	$final_array['x-amz-server-side-encryption'] = "AES256";
	$final_array['X-Amz-Credential'] = $s3key.'/'.$currentDate2.'/'.$s3region.'/s3/aws4_request';	
	$final_array['X-Amz-Algorithm'] = "AWS4-HMAC-SHA256";		
	$final_array['X-Amz-Date'] = $currentDate2.'T000000Z';
	$final_array['x-amz-meta-tag'] = '';

	$final_array['Policy'] = base64Signiture($currentDate,$currentDate2);
	$final_array['X-Amz-Signature'] = s3Signiture($currentDate,$currentDate2);


	echo json_encode($final_array);

}




?>