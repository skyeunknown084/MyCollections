<?php

//Template
// $app->Method('/APIURLPATH/', function ($request, $response, $args) {
	

// 	$headers 		= $request->getHeaders();
// 	$email 			= getheadervalue($headers,"Email");
// 	$key 			= getheadervalue($headers,"Key");
//	$jsonbody 		= $request->getParsedBody();
// 	$response = checktoken
	

// 	switch ($response) {
// 		case 'success':
		
// 			break;
// 		case 'no records':
// 			echo '{"response":"failed","errcode":"b2ber006"}';
// 			break;
// 		case 'fail':
// 			echo '{"response":"failed","errcode":"b2ber007"}';
// 			break;
// 		case 'parameters empty':
// 			echo '{"response":"failed","errcode":"b2ber001"}';
// 			break;
// 		default:
// 			echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
// 			break;
// 	}
	 
	
// });

//include all prereq php
include("scp_functions.php");
include("recommendedProdMngt.php");
include("productManagement.php");
include("notificationMngt.php");
include("settingsManagement.php");
include("globalLocal.php");
include("parserfunction.php");
include("userMngt.php");
include("advertisement.php");
include("digitalsignage.php");
include("skinanalysis.php");
include("photoUpload.php");
include("hiskin.php");
include("memberManagement.php");
include("version.php");
include("photofilepath.php");
include("channelDevice.php");
include("s3_function.php");



// api here

//GET Method
$app->get('/GetProductList/{type}', function ($request, $response, $args) {
	
	/*
	description:This api gets and displays all product list from database
	function call from productManagement.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$type           = $args['type'];
	
	if(channelChecker($channelid)){
		getProductList($type,$channelid);
	}
	 
});

$app->get('/GetProducDetails/{product_id}/{type}', function ($request, $response, $args) {
	
	/*
	description:This api gets and displays details for a product from database
	function call from productManagement.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 			= getheadervalue($headers,"channelid");
	$prodID = $args['product_id'];
	$type = $args['type'];
	
	if(channelChecker($channelid)){
		getProductDetails($prodID, $type, $channelid);
	}	
	 
});


$app->get('/getVersion/', function ($request, $response, $args) {
	/*
	get version
	*/
	global $version;
	echo $version;
	 
});

$app->get('/GetAdvertisementList', function ($request, $response, $args) {

	/* 
		function include in  advertisement.php
		description: Get List for Advertisement 
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");

	if(channelChecker($channelid)){
		getadvertisementlist($channelid);
	}
	
});
$app->get('/GetTotalSpace', function ($request, $response, $args) {

	/* 
		function include in  advertisement.php
		description: Get available space for advertisement 
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		getTotalspace($channelid);
	} 
	
});

$app->get('/GetAdvertismentDetails/{product_id}', function ($request, $response, $args) {
	
	/*
	description:This api gets and displays the details advertisement from database
	function call from productManagement.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 			= getheadervalue($headers,"channelid");
	$prodID = $args['product_id'];
	
	if(channelChecker($channelid)){
		advertisementgetdetails($prodID, $channelid);
	} 
});

$app->get('/GetDigitalSignList/', function ($request, $response, $args) {

	/* 
		function include in  advertisement.php
		description: Get List for Advertisement 
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		getdigitalsignlist($channelid);
	}	 
	
});

$app->get('/SkinAnalysis/Total/', function ($request, $response, $args) {

	/* 
		function include in skindata.php
		description: Get total count for Skin Analysis
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");

	if(channelChecker($channelid)){
		skinanalysistotal($channelid);
	} 
	
});

$app->get('/GetSkinAnalysisTotalList', function ($request, $response, $args) {
	
	/*
	description:This api gets and displays all product list from database
	function call from skinanalysis.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$jsonbody 		= $request->getParsedBody();

	if(channelChecker($channelid)){
		skinanalysistotallist($channelid);
	}
	 
});


$app->get('/GetSkinDataFilterbyidentifier/{identifier}', function ($request, $response, $args) {
	
	/*
	description: Get list by member_id
	function call from skinanalysis.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$identifier = $args['identifier'];
	$jsonbody 		= $request->getParsedBody();

	if(channelChecker($channelid)){
		getskindatalistbyIdentifier($identifier,$channelid);
	}
	 
});
$app->get('/GetHiSkinDataFilterbyidentifier/{identifier}', function ($request, $response, $args) {
	
	/*
	description: Get list by member_id
	function call from skinanalysis.php
	*/

	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$identifier = $args['identifier'];
	$jsonbody 		= $request->getParsedBody();

	if(channelChecker($channelid)){
		gethiskinlistbyIdentifier($identifier, $channelid);
	}
	 
});

$app->get('/GetTotalProduct/{type}', function ($request, $response, $args) {
	
	/*
	description:This api displays the count of all product records in the database
	function call from productManagement.php
	*/

	$headers 		= $request->getHeaders();
	$jsonbody 		= $request->getParsedBody();
	$type           = $args['type'];

	if(channelChecker($channelid)){
		getTotalProductCount($type);
	}
	 
});

$app->get('/GetRecommendProductList', function ($request, $response, $args) {
	
	/*
	description:This api displays  all recommended product packages from the database
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		$jsonbody 		= $request->getParsedBody();
		
		if(channelChecker($channelid)){
			getRecommendedList($channelid);		
		}
	 
});

$app->get('/GetRecomendPackageDeatails/{packageid}', function ($request, $response, $args) {
	
	/*
	description:This api displays  all recommended product packages from the database
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		
		$response = "success";
		$pacakgeid = $args['packageid'];

		switch ($response) {
			case 'success':
				getPackageDetails($pacakgeid);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->get('/GetRecomendRuleList', function ($request, $response, $args) {
	
	/*
	description:This api displays  all recommended product packages from the database
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
	//	$block_name     = $args['block_name'];

		$response = "success";

		switch ($response) {
			case 'success':
				getRecommendedRuleList();			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->get('/GetRecommendRulePackageDetails/{recommend_rule_id}/{rule_name}/{block_name}', function ($request, $response, $args) {
	
	/*
	description:This api displays lists specific recommended product packages from the database
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$recommend_rule_id = $args['recommend_rule_id'];
		$rule_name      = $args['rule_name'];
		$block_name     = $args['block_name'];
		
		$response = "success";
		$pacakgeid = $args['packageid'];

		switch ($response) {
			case 'success':
				getRecommendedRulePackageList($recommend_rule_id, $rule_name, $block_name);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->get('/GetHiSkinList', function ($request, $response, $args) {
	
	/*
	description:This api displays  all list for HiSkin from the database
	function call from hiskin.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			hiskintotallist($channelid);		
		}
	 
});

$app->get('/GetHiSkinTotalCount', function ($request, $response, $args) {
	
	/*
	description:This api displays  all list for HiSkin from the database
	function call from hiskin.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			hiskintotalCount($channelid);	
		}
	 
});


$app->get('/GetDropdownListProduct', function ($request, $response, $args) {
	
	/*
	description:This api displays  all list for HiSkin from the database
	function call from hiskin.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			getdropdownlistproduct($channelid);		
		}
	 
});

$app->get('/GetMemberAllList', function ($request, $response, $args) {
	
	/*
	description:This api displays  all list for Member Management from the database
	function call from memberManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			memberManagementList($channelid);	
		}
	 
});

$app->get('/GetMemberTotal', function ($request, $response, $args) {
	
	/*
	description:This api displays  all list for Member Management from the database
	function call from memberManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			memberManagementTotal($channelid);	
		}
	 
});

$app->get('/GetPendingNotification', function ($request, $response, $args) {
	
	/*
	description:This api displays count of all pending notifications from user
	function call from notificationMngt.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			getNotifications($channelid);	
		}
	 
});

$app->get('/GetUserSettings', function ($request, $response, $args) {
	
	/*
	description:This api displays available settings from user
	function call from notificationMngt.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");
		
		if(channelChecker($channelid)){
			getUserSettings($channelid);	
		}
	 
});

$app->get('/GetChannelDeviceList', function ($request, $response, $args) {
	
	/*
	description:This api displays Channel Device List
	function call from channelDevice.php
	*/

		$headers 		= $request->getHeaders();
		$channelid 		= getheadervalue($headers,"channelid");

		if(channelChecker($channelid)){
			channelDeviceList($channelid);			
		}
	 
});



//POST MEthod
$app->post('/signin/', function ($request, $response, $args) {
	
	/*
	description:This api is for user login
	function call from userManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channel_name   = getheadervalue($headers,"channelName");
		$username 		= getheadervalue($headers,"username");
		$password 		= getheadervalue($headers,"password");
		$jsonbody 		= $request->getParsedBody();
		$response = "success";
		

		switch ($response) {
			case 'success':
				login($username,$password,$channel_name);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->post('/GetFilteredProductList', function ($request, $response, $args) {
	
	/*
	description:This api gets and displays all filtered product list from database
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$response = "success";
		

		switch ($response) {
			case 'success':
				getFilteredProductList($jsonbody);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->post('/SkinAnalysis/List/', function ($request, $response, $args) {
	/* 
		function include in skindata.php
		description: Shows total list for Skin Analysis
	*/
	
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$jsonbody 		= $request->getParsedBody();
	
	if(channelChecker($channelid)){
		skinanalysislist($jsonbody,$channelid);			
	}	 
	
});

$app->post('/SkinAnalysis/Filter/', function ($request, $response, $args) {
	/* 
		function include in skindata.php
		description: Get count for filter data for Skin Analysis
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$jsonbody 		= $request->getParsedBody();

	if(channelChecker($channelid)){
		skinanalysisfilter($jsonbody,$channelid);			
	}
});

$app->post('/CreateProduct/{type}', function ($request, $response, $args) {
	
	/*
	description:This api creates new a product entry
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$channelid 		= getheadervalue($headers,"channelid");
		$type           = $args['type'];

		if(channelChecker($channelid)){
			createNewProd($jsonbody, $type, $channelid);			
		}
	 
});

$app->post('/CreateProductMeta/{metatype}/{metaname}', function ($request, $response, $args) {
	
	/*
	description:This api creates new a product brand entry
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$phone_number   = getheadervalue($headers,"phone_number");
		$key 			= getheadervalue($headers,"Key");
		$channelid 		= getheadervalue($headers,"channelid");
		$metaname           = $args['metaname'];
		$metatype           = $args['metatype'];

		if(channelChecker($channelid)){
			createnewmeta($metatype, $metaname, $channelid);			
		}
	 
});

$app->post('/CreateRecommendedProduct', function ($request, $response, $args) {
	
	/*
	description:This api creates new Recommended Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$channelid 		= getheadervalue($headers,"channelid");

		if(channelChecker($channelid)){
			addRecommendedProduct($jsonbody,$channelid);			
		}
	 
});


$app->post('/CreateAdvertisment/', function ($request, $response, $args) {
 /* 
  function include in advertisement.php
  description: Create new entry for Advertisement
 */
	$headers   = $request->getHeaders();
	$channelid    = getheadervalue($headers,"channelid");
	$jsonbody   = $request->getParsedBody();
	
	if(channelChecker($channelid)){
		createnewadvertisement($jsonbody,$channelid);			
	}
});

$app->post('/CreateDigitalSignage/', function ($request, $response, $args) {
 /* 
  function include in advertisement.php
  description: Create new entry for DigitalSignage
 */
	$headers   = $request->getHeaders();
	$channelid    = getheadervalue($headers,"channelid");
	$jsonbody   = $request->getParsedBody();
	
	if(channelChecker($channelid)){
		createnewdigitalSign($jsonbody,$channelid);			
	}
});

$app->post('/CreateRecommendedRule/', function ($request, $response, $args) {
	/* 
		function include in advertisement.php
		description: Create new entry for Advertisement
	*/
	$headers 		= $request->getHeaders();
	$jsonbody 		= $request->getParsedBody();
	$response = "success";
	
	switch ($response) {
		case 'success':
			addRecommendedRule($jsonbody);
			break;
		case 'no records':
			echo '{"response":"failed","errcode":"b2ber006"}';
			break;
		case 'fail':
			echo '{"response":"failed","errcode":"b2ber007"}';
			break;
		case 'parameters empty':
			echo '{"response":"failed","errcode":"b2ber001"}';
			break;
		default:
			echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
			break;
	}	
});

$app->post('/CreateProductUnrestricted', function ($request, $response, $args) {
	
	/*
	description:This api creates new Unrestricted Product
	function call from productManagement.php 
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$response = "success";
		

		switch ($response) {
			case 'success':
				createprodunrestricted($jsonbody);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->post('/uploadProductPhoto/', function ($request, $response, $args) {
	
	/*
	description:This api creates new Recommended Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$response = "success";

		if(isset($_FILES)){
			switch ($response) {
			case 'success':
				uploadBlob($_FILES);
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
			
		}else{
			echo "no file";
		}	
		

		
	 
});
$app->post('/removePhotoFromTempDir/', function ($request, $response, $args) {
	
	/*
	Unlink Files to be uploaded
	*/

		$headers 		= $request->getHeaders();
		$filepath 			= getheadervalue($headers,"filepath");
		$response = "success";
		
			switch ($response) {
			case 'success':
				unlink("/var/www/html/src/".$filepath);
				echo "success";
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
			
});

$app->post('/HiSkinFilterList', function ($request, $response, $args) {
	/* 
		function include in hiskin.php
		description: Get List of filter data for HiSkin
	*/
	$headers 		= $request->getHeaders();
	$jsonbody 		= $request->getParsedBody();
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		hiskinfilterlist($jsonbody,$channelid);
	}
});

$app->post('/MemberManagement/FilterList/', function ($request, $response, $args) {
	/* 
		function include in skindata.php
		description: Shows total list for Skin Analysis
	*/
	
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	$jsonbody 		= $request->getParsedBody();

	if(channelChecker($channelid)){
		memberManagementFilterSearch($jsonbody,$channelid);
	}	 
	
});

$app->post('/CreateNewMember/', function ($request, $response, $args) {
 /* 
  function include in advertisement.php
  description: Create new entry for Advertisement
 */
	$headers   = $request->getHeaders();
	$channelid    = getheadervalue($headers,"channelid");
	$jsonbody   = $request->getParsedBody();
	
	if(channelChecker($channelid)){
		createnewMember($jsonbody,$channelid);
	}
});

$app->post('/ChangeOrderAdvertisement', function ($request, $response, $args) {
 /* 
  function include in advertisement.php
  description: Create new entry for Advertisement
 */
	$headers   = $request->getHeaders();
	$channelid    = getheadervalue($headers,"channelid");
	$jsonbody   = $request->getParsedBody();
	
	if(channelChecker($channelid)){
		changeorderadvertisement($jsonbody,$channelid);
	}
});

$app->post('/ChangeOrderDigitalSignage', function ($request, $response, $args) {
 /* 
  function include in advertisement.php
  description: Create new entry for Advertisement
 */
	$headers   = $request->getHeaders();
	$channelid    = getheadervalue($headers,"channelid");
	$jsonbody   = $request->getParsedBody();
	
	if(channelChecker($channelid)){
		changeorderdigitalsignage($jsonbody,$channelid);
	}
});

$app->post('/CreateSettings', function ($request, $response, $args) {
	/* 
		function include in settingsManagement.php
		description: Create settings for a specific channel
	*/
	
	$headers 		= $request->getHeaders();
	$jsonbody 		= $request->getParsedBody();
	$response = "success";
	
	switch ($response) {
		case 'success':
			createSettings($jsonbody);
			break;
		case 'no records':
			echo '{"response":"failed","errcode":"b2ber006"}';
			break;
		case 'fail':
			echo '{"response":"failed","errcode":"b2ber007"}';
			break;
		case 'parameters empty':
			echo '{"response":"failed","errcode":"b2ber001"}';
			break;
		default:
			echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
			break;
	}	 
	
});

$app->post('/ChannelDeviceFilterList', function ($request, $response, $args) {
	/* 
		function include in skindata.php
		description: Shows total list for Skin Analysis
	*/
	
	$headers 		= $request->getHeaders();
	$body 		= $request->getParsedBody();
	$adminAccount    = getheadervalue($headers,"adminAccount");

	if(adminChecker($adminAccount)){
		channelDeviceFilterList($body);
	}	 
	
});


//PUT Method
$app->put('/EditProduct/{product_id}/{type}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing product entry
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$prodID = $args['product_id'];
		$type = $args['type'];

		if(channelChecker($channelid)){
			editProd($prodID, $jsonbody, $type, $channelid);
		}
});

$app->put('/Editadvertisement/{advertisement_id}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing entry for advertisement
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$adverID = $args['advertisement_id'];
		
		if(channelChecker($channelid)){
			editadvertisement($adverID, $jsonbody, $channelid);	
		}
});


$app->put('/EditRecommendedProduct/{package_id}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing Recommended Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$package_id     = $args['package_id'];
		
		if(channelChecker($channelid)){
			editRecommendedProduct($channelid,$package_id,$jsonbody);
		}
	 
});

$app->put('/EditProductOrder/{package_id}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing Recommended Product List Order from a Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$package_id     = $args['package_id'];
		$response = "success";
		

		switch ($response) {
			case 'success':
				editProductOrder($package_id,$jsonbody);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->put('/EditRecommendedRuleOrder/{recommend_rule_id}/{rule_name}/{block_name}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing Recommended Rule package Order
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$recommend_rule_id     = $args['recommend_rule_id'];
		$rule_name      = $args['rule_name'];
		$block_name     = $args['block_name'];
		$response = "success";
		

		switch ($response) {
			case 'success':
				editRecommendedRuleOrder($recommend_rule_id,$rule_name,$block_name,$jsonbody);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->put('/EditMember/{member_id}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing entry for advertisement
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$memID = $args['member_id'];		
	
		if(channelChecker($channelid)){
			editMember($memID, $jsonbody, $channelid);
		}
});

$app->put('/EditSettings', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing entry for settings
	function call from settingsMngt.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		
		if(channelChecker($channelid)){
			editSettings($jsonbody, $channelid);
		}
});

$app->put('/EditDigitalSignage/{id}', function ($request, $response, $args) {
	
	/*
	description:This api edits an existing entry for Digital Signage function
	 call from digitalsignage.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$id = $args['id'];
		
		if(channelChecker($channelid)){
			EditDigitalSignage($jsonbody, $channelid,$id);
		}
});


//DELETE Method
$app->delete('/DeleteProduct/{product_id}/{type}', function ($request, $response, $args) {
	
	/*
	description:This api deletes an existing product entry
	function call from productManagement.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$response = "success";
		$prodID = $args['product_id'];
		$type = $args['type'];

		switch ($response) {
			case 'success':
				deleteProd($prodID, $type);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->delete('/DeleteRecommendedProduct/{package_id}', function ($request, $response, $args) {
	
	/*
	description:This api deletes an existing Recommended Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$channelid      = getheadervalue($headers,"channelid"); 
		$jsonbody 		= $request->getParsedBody();
		$package_id     = $args['package_id'];
		$response = "success";
		

		switch ($response) {
			case 'success':
				deleteRecommendedProduct($package_id);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->delete('/DeleteRecommendRule/{rule_id}', function ($request, $response, $args) {
	
	/*
	description:This api deletes an existing Recommended Product Package
	function call from recommendedProdMngt.php
	*/

		$headers 		= $request->getHeaders();
		$jsonbody 		= $request->getParsedBody();
		$rule_id     = $args['rule_id'];
		$response = "success";
		

		switch ($response) {
			case 'success':
				deleteRecommendedRule($rule_id);			
				break;
			case 'no records':
				echo '{"response":"failed","errcode":"b2ber006"}';
				break;
			case 'fail':
				echo '{"response":"failed","errcode":"b2ber007"}';
				break;
			case 'parameters empty':
				echo '{"response":"failed","errcode":"b2ber001"}';
				break;
			default:
				echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
				break;
		}
	 
});

$app->delete('/DeleteAdvertisment/{ad_id}', function ($request, $response, $args) {
	/* 
		function include in hiskin.php
		description: Get count for filter data for Skin Analysis
	*/
	$headers 		= $request->getHeaders();
	$ad_id 			= $args['ad_id'];
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		deleteadvertisementlist($ad_id, $channelid);
	}	
});

$app->delete('/DeleteMember/{memID}', function ($request, $response, $args) {
	/* 
		function include in hiskin.php
		description: Get count for filter data for Skin Analysis
	*/
	$headers 		= $request->getHeaders();
	$memID 			= $args['memID'];
	$channelid 		= getheadervalue($headers,"channelid");
	$response = "success";
	
	switch ($response) {
		case 'success':
			deleteMember($memID);
			break;
		case 'no records':
			echo '{"response":"failed","errcode":"b2ber006"}';
			break;
		case 'fail':
			echo '{"response":"failed","errcode":"b2ber007"}';
			break;
		case 'parameters empty':
			echo '{"response":"failed","errcode":"b2ber001"}';
			break;
		default:
			echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
			break;
	}
	

});

$app->delete('/DeleteDigitalSignage/{id}', function ($request, $response, $args) {
	/* 
		Set status 0 for digital signage for specific id
	*/
	$headers 		= $request->getHeaders();
	$id 			= $args['id'];
	$channelid 		= getheadervalue($headers,"channelid");
	$response = "success";
	
	switch ($response) {
		case 'success':
			deleteDigitalSignage($id);
			break;
		case 'no records':
			echo '{"response":"failed","errcode":"b2ber006"}';
			break;
		case 'fail':
			echo '{"response":"failed","errcode":"b2ber007"}';
			break;
		case 'parameters empty':
			echo '{"response":"failed","errcode":"b2ber001"}';
			break;
		default:
			echo '{"response":"failed","errcode":"b2ber101","message":"'.$response.'"}';
			break;
	}	
});

$app->post('/GetBucketName/', function ($request, $response, $args) {
	/* 
		Set status 0 for digital signage for specific id
	*/
	global $bucketname;
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		echo $bucketname;
	}		
});

$app->post('/GetS3Policy/', function ($request, $response, $args) {
	/* 
		Set status 0 for digital signage for specific id
	*/
	$headers 		= $request->getHeaders();
	$channelid 		= getheadervalue($headers,"channelid");
	
	if(channelChecker($channelid)){
		s3Policy();
	}		
});

$app->get('/Redirect/', function ($request, $response, $args) {
	/* 
		Set status 0 for digital signage for specific id
	*/
	
	echo "Success";		
});
?>