<?php

include("globalLocal.php");
include("database.php");
include("historyMngt.php");
include ($_SERVER['DOCUMENT_ROOT']."/aws/upload.php");

function getProductList($type,$channelid){
	$finalArray = array();
	$prodArray = array();
	$photoarray = array();
	$dbLocal = getdbhimirrorqa();
	$idArray = array();
	$selectQSkin = "Select USRChannelProducts.*,P_age.age_value,P_class.class_id,P_class.class_type,P_ritual.beauty_ritual,P_skintype.skin_type,P_photo.photo_type,P_photo.photo_link,P_photo.s3_path ,P_price.order_index,P_price.prices_dollar_sign,P_price.prices,P_barcode.barcode_type,P_barcode.barcode_value,P_purchase.purchase_type,P_purchase.purchase_link,P_size.size_type,P_size.size_value,P_concern.concerns_id,P_concern.concerns_value from USRChannelProducts LEFT JOIN (SELECT product_id,age_value, MAX(update_time) AS maxdate
	 FROM USRChannelProductCustomerAge
	 GROUP BY product_id) as P_age on P_age.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,class_id,class_type, MAX(update_time) AS maxdatecat
	 FROM USRChannelProductCustomerClass
	 GROUP BY product_id) as P_class on P_class.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,beauty_ritual,
	 MAX(update_time) AS maxdateritual
	 FROM USRChannelProductCustomerBeautyRitual
	 GROUP BY product_id) as P_ritual on P_ritual.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,skin_type,
	 MAX(update_time) AS maxdateskintype
	 FROM USRChannelProductCustomerSkintype
	 GROUP BY product_id) as P_skintype on P_skintype.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,photo_type,photo_link,s3_path, MAX(update_time) AS maxdatephoto
	 FROM USRChannelProductPhoto
	 GROUP BY product_id) as P_photo on P_photo.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,order_index,prices_dollar_sign,prices, MAX(update_time) AS maxdateprice
	 FROM USRChannelProductPrice
	 GROUP BY product_id) as P_price on P_price.product_id=USRChannelProducts.id  LEFT JOIN (SELECT product_id,barcode_type,barcode_value, MAX(update_time) AS maxdatebarcode
	 FROM USRChannelProductsBarcode
	 GROUP BY product_id) as P_barcode on P_barcode.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,purchase_type,purchase_link, MAX(update_time) AS maxdatepurchase
	 FROM USRChannelProductsPurchaseInfo
	 GROUP BY product_id) as P_purchase on P_purchase.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,size_type,size_value,
	 MAX(update_time) AS maxdatesize
	 FROM USRChannelProductsSize
	 GROUP BY product_id) as P_size on P_size.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,concerns_id,concerns_value, MAX(update_time) AS maxdateconcern
	 FROM USRChannelProductCustomerConcerns
	 GROUP BY product_id) as P_concern on P_concern.product_id=USRChannelProducts.id WHERE USRChannelProducts.is_deleted =0 AND USRChannelProducts.syschannelprofiles_id = '$channelid' ORDER BY create_time DESC";

	$selectQUnres = "SELECT USRChannelUnrestrictedProduct.*, P_price.prices, P_price.prices_dollar_sign, P_photo.photo_type, P_photo.photo_link from USRChannelUnrestrictedProduct LEFT JOIN (SELECT unrestricted_product_id,prices,prices_dollar_sign, MAX(update_time) AS maxdateprice FROM USRChannelProductPrice GROUP BY unrestricted_product_id) as P_price on P_price.unrestricted_product_id=USRChannelUnrestrictedProduct.id LEFT JOIN (SELECT unrestricted_product_id, photo_type, photo_link, MAX(update_time) AS maxdatephoto FROM USRChannelProductPhoto GROUP BY unrestricted_product_id) as P_photo on P_photo.unrestricted_product_id=USRChannelUnrestrictedProduct.id WHERE USRChannelUnrestrictedProduct.is_deleted = 0 AND USRChannelUnrestrictedProduct.syschannelprofiles_id = '$channelid' ORDER BY USRChannelUnrestrictedProduct.insert_time DESC";

	if($type == 'skincare'){
		try{
			$stmt = $dbLocal->query($selectQSkin);
			$stmt->execute();
			$result= $stmt->fetchAll();
			echo json_encode(array("SkinCare"=>$result));
		}catch(PDOException $e){
			echo $e.'{"response":"failed","errcode":"b2ber1201","message":"failed to get skincare Product List"}';
		}
	}elseif($type == 'unrestricted'){
		try{
			$stmt = $dbLocal->prepare($selectQUnres);
			if($stmt->execute()){
				$res = $stmt->fetchAll();
				echo json_encode(array("unrestricted"=>$res));
			}else
				echo "execute error";
		}catch(PDOException $e){
			echo $e.'{"response":"failed","errcode":"b2ber1202","message":"failed to get unrestricted Product List"}';
		}
	}else{
		$allProdArr = array();
		try{
			$stmt = $dbLocal->query($selectQSkin);
			$stmt->execute();
			$result= $stmt->fetchAll();
			$allProdArr['skincare'] = $result;
		}catch(PDOException $e){
			echo '{"response":"failed","errcode":"b2ber1203","message":"failed to get Skincare and unrestricted Product List"}';
		}
		try{
			$stmt2 = $dbLocal->query($selectQUnres);
			if($stmt2->execute()){
				$result2 = $stmt2->fetchAll();
			$allProdArr['unrestricted'] = $result2;
			}else
				echo "execute error";
		}catch(PDOException $e){
			echo '{"response":"failed","errcode":"b2ber1203","message":"failed to get Skincare and unrestricted Product List"}';
		}

		echo json_encode($allProdArr);
	}
}

function getProductDetails($prodid, $type,$channelid){
	$finalArray = array();

	$selectQSkin = "Select USRChannelProducts.*,P_class.class_id,P_class.class_type,P_price.prices,P_barcode.barcode_type,P_barcode.barcode_value,P_purchase.purchase_type,P_purchase.purchase_link,P_size.size_type,P_size.size_value from USRChannelProducts LEFT JOIN (SELECT product_id,age_value, MAX(update_time) AS maxdate
	 FROM USRChannelProductCustomerAge
	 GROUP BY product_id) as P_age on P_age.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,class_id,class_type, MAX(update_time) AS maxdatecat
	 FROM USRChannelProductCustomerClass
	 GROUP BY product_id) as P_class on P_class.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,beauty_ritual,
	 MAX(update_time) AS maxdateritual
	 FROM USRChannelProductCustomerBeautyRitual
	 GROUP BY product_id) as P_ritual on P_ritual.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,skin_type,
	 MAX(update_time) AS maxdateskintype
	 FROM USRChannelProductCustomerSkintype
	 GROUP BY product_id) as P_skintype on P_skintype.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,photo_type,photo_link,s3_path, MAX(update_time) AS maxdatephoto
	 FROM USRChannelProductPhoto
	 GROUP BY product_id) as P_photo on P_photo.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,order_index,prices_dollar_sign,prices, MAX(update_time) AS maxdateprice
	 FROM USRChannelProductPrice
	 GROUP BY product_id) as P_price on P_price.product_id=USRChannelProducts.id  LEFT JOIN (SELECT product_id,barcode_type,barcode_value, MAX(update_time) AS maxdatebarcode
	 FROM USRChannelProductsBarcode
	 GROUP BY product_id) as P_barcode on P_barcode.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,purchase_type,purchase_link, MAX(update_time) AS maxdatepurchase
	 FROM USRChannelProductsPurchaseInfo
	 GROUP BY product_id) as P_purchase on P_purchase.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,size_type,size_value,
	 MAX(update_time) AS maxdatesize
	 FROM USRChannelProductsSize
	 GROUP BY product_id) as P_size on P_size.product_id=USRChannelProducts.id LEFT JOIN (SELECT product_id,concerns_id,concerns_value, MAX(update_time) AS maxdateconcern
	 FROM USRChannelProductCustomerConcerns
	 GROUP BY product_id) as P_concern on P_concern.product_id=USRChannelProducts.id WHERE USRChannelProducts.is_deleted =0 AND USRChannelProducts.id = '$prodid'";	

	$selectConcernQ = "SELECT USRChannelProductCustomerConcerns.concerns_id, USRChannelProductCustomerConcerns.concerns_value from USRChannelProducts INNER JOIN USRChannelProductCustomerConcerns on USRChannelProducts.id = USRChannelProductCustomerConcerns.product_id WHERE USRChannelProducts.id = '$prodid'";

	$selectAgeQ = "SELECT USRChannelProductCustomerAge.age_value from USRChannelProducts INNER JOIN USRChannelProductCustomerAge on USRChannelProducts.id = USRChannelProductCustomerAge.product_id WHERE USRChannelProducts.id = '$prodid' and USRChannelProductCustomerAge.is_deleted = 0";

	$selectBeautyQ = "SELECT USRChannelProductCustomerBeautyRitual.beauty_ritual from USRChannelProducts INNER JOIN USRChannelProductCustomerBeautyRitual on USRChannelProducts.id = USRChannelProductCustomerBeautyRitual.product_id WHERE USRChannelProducts.id = '$prodid' and USRChannelProductCustomerBeautyRitual.is_deleted = 0";

	$selectSkinTypeQ = "SELECT USRChannelProductCustomerSkintype.skin_type from USRChannelProducts INNER JOIN USRChannelProductCustomerSkintype on USRChannelProducts.id = USRChannelProductCustomerSkintype.product_id WHERE USRChannelProducts.id = '$prodid' and USRChannelProductCustomerSkintype.is_deleted = 0";

	$selectPhotoQ = "SELECT USRChannelProductPhoto.photo_link from USRChannelProducts INNER JOIN USRChannelProductPhoto on USRChannelProducts.id = USRChannelProductPhoto.product_id WHERE USRChannelProducts.id = '$prodid' and USRChannelProductPhoto.is_deleted = 0";

	$selectQUnres = "SELECT * FROM `USRChannelUnrestrictedProduct` INNER JOIN USRChannelProductPrice ON USRChannelUnrestrictedProduct.id = USRChannelProductPrice.unrestricted_product_id WHERE  USRChannelUnrestrictedProduct.id != '' AND USRChannelUnrestrictedProduct.id != 'NULL' AND USRChannelUnrestrictedProduct.is_deleted = '0' AND USRChannelUnrestrictedProduct.id = '".$prodid."' AND USRChannelUnrestrictedProduct.syschannelprofiles_id = '$channelid'";

	$dbLocal = getdbhimirrorqa();

	if($type == 'unrestricted'){
		try{
			$stmt = $dbLocal->prepare($selectQUnres);
			if($stmt->execute()){
				$res = $stmt->fetchAll();
				$numofdata = $stmt->rowCount();
				if($numofdata > 0){

					$prodArray = array("unrestricted"=>$res);
					$photoFinarray = array();
					foreach ($res as  $value) {
						foreach ($value as $key => $value2) {
							if($key == 'id'){

								$selectQuery = "SELECT USRChannelProductPhoto.id as photoId, USRChannelProductPhoto.unrestricted_product_id as photoUnrestrictedId, photo_type, photo_link, s3_path, is_deleted, USRChannelProductPhoto.update_time as photoupdatetime FROM `USRChannelProductPhoto` WHERE unrestricted_product_id = '".$value2."' AND is_deleted = 0";								
								$stmt = $dbLocal->prepare($selectQuery);
								if($stmt->execute()){
									$res2 = $stmt->fetchAll();
									foreach ($res2 as $value) {
										foreach ($value as $key => $val) {											
											if($key == "photo_link"){
												array_push($photoFinarray, $val);
											}
										}
									}
								}		
							}
						}
					}
					$photoFinarray = array("photo_link"=>$photoFinarray);
					$counter = "";
					$temparrayProd = array();					
					foreach ($prodArray as $key => $value) {
						foreach ($value as $key2 => $val) {
							array_push($temparrayProd, $val);							
						}
					}
					for ($i=0; $i < count($temparrayProd) ; $i++) {
						$unresArray = array_merge($temparrayProd[$i],$photoFinarray);
					}
					$finalArray = array("unrestricted"=>$unresArray);
					echo json_encode($finalArray);
				}else
					echo '{"response":"no result","errcode":"b2ber004-171"}';
			}else
				echo '{"response":"SMT Execute error","errcode":"b2ber005-173"}';
		}catch(PDOException $e){
			echo '{"response":"failed","errcode":"b2ber1204","message":"failed to get unrestricted Product details"}';
		}
	}elseif($type == 'skincare'){

		$prodArray = array();
		$concernkeys = array();
		$concernvalues = array();
		$concernArray = array();
		$ageArray = array();
		$beautyArray = array();
		$skinTArray = array();
		$photoArray = array();

		try{
			$stmtget = $dbLocal->query($selectQSkin);
			$stmtget->execute();	
			$result= $stmtget->fetchAll();

			foreach($result[0] as $rkey => $rvalue){
				$prodArray[$rkey] = $rvalue;
			}

			$stmtgetAge = $dbLocal->query($selectAgeQ);
			$stmtgetAge->execute();	
			$result2= $stmtgetAge->fetchAll();

			for($i = 0;$i<count($result2); $i++){
				$ageArray[] = $result2[$i]['age_value'];
			}

			$stmtgetBeauty = $dbLocal->query($selectBeautyQ);
			$stmtgetBeauty->execute();	
			$result4= $stmtgetBeauty->fetchAll();
			
			for($i = 0;$i<count($result4); $i++){
				$beautyArray[] = $result4[$i]['beauty_ritual'];
			}	

			$stmtgetSkin = $dbLocal->query($selectSkinTypeQ);
			$stmtgetSkin->execute();	
			$result5= $stmtgetSkin->fetchAll();
			
			for($i = 0;$i<count($result5); $i++){
				$skinTArray[] = $result5[$i]['skin_type'];
			}				

			$stmtgetPhoto = $dbLocal->query($selectPhotoQ);
			$stmtgetPhoto->execute();	
			$result6= $stmtgetPhoto->fetchAll();

			for($i = 0;$i<count($result6); $i++){
				$photoArray[] = $result6[$i]['photo_link'];
			}

			$stmtgetC = $dbLocal->query($selectConcernQ);
			$stmtgetC->execute();	
			$result3= $stmtgetC->fetchAll();
			
			for($i =0; $i<count($result3); $i++){
				foreach($result3[$i] as $key => $value){
					if($key == 'concerns_id')
						$concernkeys[] = $value;
					elseif($key == 'concerns_value')
						$concernvalues[] = $value;
				}	
			}

			$concernArray = array_combine($concernkeys, $concernvalues);
			
			foreach($concernArray as $ckey => $cvalue){
				$prodArray[$ckey] = $cvalue;
			}

			$prodArray["age_value"] = $ageArray;
			$prodArray["beauty_ritual"] = $beautyArray;
			$prodArray["skin_type"] = $skinTArray;
			$prodArray["photo_link"] = $photoArray;

			echo json_encode($prodArray);
		}
		catch(PDOException $e){
			echo '{"response":"failed","errcode":"b2ber1205","message":"failed to get skincare Product details"}';
			echo "\n $selectQSkin";
		}
	}
}


function getTotalProductCount($type){
	
	$selectSkinCountQ = "SELECT COUNT(USRChannelProducts.id) as totalCount from USRChannelProductCategory INNER JOIN USRChannelProducts ON USRChannelProducts.id = USRChannelProductCategory.product_id INNER JOIN USRChannelProductCustomerAge ON USRChannelProducts.id = USRChannelProductCustomerAge.product_id INNER JOIN USRChannelProductCustomerBeautyRitual ON USRChannelProducts.id = USRChannelProductCustomerBeautyRitual.product_id INNER JOIN USRChannelProductCustomerClass ON USRChannelProducts.id = USRChannelProductCustomerClass.product_id INNER JOIN USRChannelProductPhoto ON USRChannelProducts.id = USRChannelProductPhoto.product_id INNER JOIN USRChannelProductPrice ON USRChannelProducts.id = USRChannelProductPrice.product_id INNER JOIN USRChannelProductRecommendations ON USRChannelProducts.id = USRChannelProductRecommendations.product_id INNER JOIN USRChannelProductsBarcode ON USRChannelProducts.id = USRChannelProductsBarcode.product_id INNER JOIN USRChannelProductCustomerSkintype ON USRChannelProducts.id = USRChannelProductCustomerSkintype.product_id INNER JOIN USRChannelProductsPurchaseInfo ON USRChannelProducts.id = USRChannelProductsPurchaseInfo.product_id INNER JOIN USRChannelProductsSize ON USRChannelProducts.id = USRChannelProductsSize.product_id WHERE USRChannelProductCategory.category_id !='NULL'";

	$selectUnresCountQ = "SELECT COUNT(USRChannelUnrestrictedProduct.id) as totalCount FROM USRChannelUnrestrictedProduct INNER JOIN USRChannelProductPhoto ON USRChannelUnrestrictedProduct.id = USRChannelProductPhoto.unrestricted_product_id";
	
	$dbLocal = getdbhimirrorqa();

	try{
		if($type == 'unrestricted'){
			$stmtget = $dbLocal->prepare($selectUnresCountQ);
			$stmtget->execute();	
			$result= $stmtget->fetchAll();
			foreach($result[0] as $key => $val){
				echo json_encode(array("totalCount"=>$val));
			}
		}elseif($type == 'skincare'){
			$stmtget = $dbLocal->prepare($selectSkinCountQ);
			$stmtget->execute();	
			$result= $stmtget->fetchAll();
			foreach($result[0] as $key => $val){
				echo json_encode(array("totalCount"=>$val));
			}
		}
	}
	catch(PDOException $e){
		echo '{"response":"failed","errcode":"b2ber1206","message":"failed to get product count"}';
	}
}


function getFilteredProductList($filterjson){

	$type = $filterjson['type'];
	$currency = $filterjson['currency'];
	$whereClause = "";

	if(count($type) > 0 && count($currency) > 0){
  		$whereClause = "WHERE type IN (".listAll($type).") AND currency IN (".listAll($currency).")";
 	}else if(count($type) == 0 && count($currency)>0){
  		$whereClause = "WHERE currency IN (".listAll($currency).")";
 	}else if(count($type) > 0 && count($currency) == 0){
  		$whereClause = "WHERE type IN (".listAll($type).")";
 	}else{
  		$whereClause = "";
 	}

	try{
		$dbLocal = getdbhimirrorqa();
		$prodListArr = array("filteredProductList");
		$stmtget = $dbLocal->prepare("SELECT * FROM B2BSkinCareProduct $whereClause");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		array_push($prodListArr, $result);
		echo json_encode($prodListArr);
	}
	catch(PDOException $e){
		echo '{"response":"failed","errcode":"b2ber1207","message":"failed to get filtered product count"}';
	}
}

function createNewProd($jsonbody, $type, $channel_id){

	$dbLocal = getdbhimirrorqa();
	global $dbnamelocal,$ProductPhotoFilepath;	
	$channel_name = "";
	try{		
		$getChannelNameQ = "SELECT name from SYSChannelProfiles WHERE id = '$channel_id'";
		$smtgetname = $dbLocal->prepare($getChannelNameQ);
		$smtgetname->execute();
		$channel_name_raw = $smtgetname->fetchAll();
		$channel_name = $channel_name_raw[0]['name'];
	}catch(PDOException $e){
		echo "FAILED 335".$e->getMessage();
	}

	if($type == 'unrestricted'){
		$checkFlag1 = false;
		$checkFlag2 = false;
		$checkFlag3 = false;
		$lastIDProduct = "";
		try{
			$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelUnrestrictedProduct'");
			$stmtgetProduct->execute();	
			$result= $stmtgetProduct->fetchAll();	
			$keylist="";
			$valuelist="";

			foreach ($jsonbody as $key => $value){
				$val=getInsertQueryProduct($key,$value,$result);
					if(count($val)!=0){
						if(empty($keylist)){
							$keylist.=$val['key'];
							$valuelist.=$val['value'];
						}else{
							$keylist.=",".$val['key'];
							$valuelist.=",".$val['value'];
						}
					}
			}

			$insertProduct="insert into USRChannelUnrestrictedProduct ($keylist,syschannelprofiles_id,insert_time) VALUES ($valuelist,'$channel_id',CURRENT_TIMESTAMP)";
			$stmtInsert = $dbLocal->prepare($insertProduct);

			$stmtInsert->execute();
			$stmtLastInsertID = $dbLocal->prepare("SELECT id from USRChannelUnrestrictedProduct ORDER by insert_time DESC LIMIT 1");
			$stmtLastInsertID->execute();
			$getLastIDresult = $stmtLastInsertID->fetchAll();
			$lastIDProduct = $getLastIDresult[0]['id'];
			$checkFlag1 = true;
		}catch(PDOException $e){
			echo '{"response":"PDO Exception","errcode":"b2ber003-375"}';
			$checkFlag1 = false;
		}
		if($checkFlag1){
			try{
				$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPrice'");
				$stmtgetProduct->execute();	
				$result= $stmtgetProduct->fetchAll();	
				$keylist="";
				$valuelist="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result);
						if(count($val)!=0){
							if(empty($keylist)){
								$keylist.=$val['key'];
								$valuelist.=$val['value'];
							}else{
								$keylist.=",".$val['key'];
								$valuelist.=",".$val['value'];
							}
						}
				}

				$insertPrice="insert into USRChannelProductPrice ($keylist,unrestricted_product_id,product_id,update_time) VALUES ($valuelist,'$lastIDProduct','N/A',CURRENT_TIMESTAMP)";
				$stmtInsert = $dbLocal->prepare($insertPrice);
				$stmtInsert->execute();
				$checkFlag2 = true;
			}catch(PDOException $e){
				echo $e->getMessage()."401 USRChannelProductPrice\n";
				$checkFlag2 = false;
			}
		}else{
			echo "ERROR line 409";
		}
		$countr=0;
		if($checkFlag2){
			try{

				$stmtgetPhoto = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPhoto'");
				$stmtgetPhoto->execute();	
				$result11= $stmtgetPhoto->fetchAll();
				$photoLinkArray = array();
				$s3pathArray = array();
				$extensionArray = array();
				$keylist11="";
				$valuelist11="";
				$keylist = "";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result11);
						if(count($val)!=0){
							if(empty($keylist11)){								 
								 if(is_array($value)){								 	
								 	$keylist.=$val['key'];
								 	 foreach($value as $innerKey => $innerValue){
								 	 	$rand = rand(0,999);
										$time = time();
										$extension = explode(".", $innerValue);
										$extension = strtolower(end($extension));
										array_push($extensionArray, $extension);
										$hashvalue=hash('sha256',php_uname('n').$time.$rand);
										$s3path =  "$channel_name/Product/".$hashvalue.".".$extension;
										$filename = basename($innerValue);
										$filepath = $ProductPhotoFilepath."$filename";
										array_push($s3pathArray, $s3path);
										if(file_exists($filepath)){
											$photoLinkArray[] = "'".uploadtoS3($filepath,$s3path)."'";								 	 	
										}else{
											$photoLinkArray[] = 'N/A';
										}
								 	 	
								 	 }
								 }else{
								 	$keylist11.=$val['key'];
									$valuelist11.=$val['value']; 	
								 }
							}else{
								if(is_array($value)){
									$keylist.=$val['key'];
									foreach($value as $innerKey => $innerValue){
										$rand = rand(0,999);
										$time = time();
										$extension = explode(".", $innerValue);
										$extension = strtolower(end($extension));
										
										$hashvalue=hash('sha256',php_uname('n').$time.$rand);
										$s3path =  "$channel_name/Product/".$hashvalue.".".$extension;
								 	 	$filename = basename($innerValue);
										$filepath = $ProductPhotoFilepath."$filename";										
										if(file_exists($filepath)){											
											array_push($s3pathArray, $s3path);
											array_push($extensionArray, $extension);
											$photoLinkArray[] = "'".uploadtoS3($filepath,$s3path)."'";					 	 	
										}else{
											$photoLinkArray[] = 'N/A';
											array_push($s3pathArray, 'N/A');
											array_push($extensionArray, 'N/A');
										}
								 	 }
								}else{
									$keylist11.=",".$val['key'];
									$valuelist11.=",".$val['value']; 	
								 }
							}
						}
				}
				for($i=0; $i<count($photoLinkArray);$i++){
					$insertPhoto ="insert into USRChannelProductPhoto ($keylist,$keylist11,s3_path,unrestricted_product_id,photo_type,product_id,update_time) VALUES ($photoLinkArray[$i],$valuelist11,'$s3pathArray[$i]','$lastIDProduct','$extensionArray[$i]','N/A',CURRENT_TIMESTAMP)";		
					$stmtInsert11 = $dbLocal->prepare($insertPhoto);
					$stmtInsert11->execute();
					$countr++;
					$checkFlag3 = true;
				}
				$checkFlag3 = true;
			}catch(PDOException $e){
				echo $e->getMessage()."760 USRChannelProductPhoto\n $insertPhoto";
				$checkFlag3 = false;
			}
		}else{
			echo "line 480 ERROR";
		}
		if($checkFlag1 == $checkFlag2 && $checkFlag1 == $checkFlag3){
			echo "success";
		}else{
			echo "line 500 ERROR";
		}
	}elseif($type == 'skincare'){

		$wrinkles = "";
		$darkspot = "";
		$hydration = "";
		$pores = "";
		$acne = "";
		$sensitivity = "";
		$briliance = "";
		$protection = "";
		$skin_clarity = "";
		$anti_aging = "";
		$skintone = "";
		$nourishing = "";
		$anti_dark_circle = "";
		$anti_puffiness = "";
		$photo_link = "";

		foreach($jsonbody as $key => $value){
			
			if($key == 'wrinkles')    				$wrinkles = str_replace("'", "''", $value);
			elseif($key == 'darkspot')    			$darkspot = str_replace("'", "''", $value);
			elseif($key == 'hydration')    			$hydration = str_replace("'", "''", $value);
			elseif($key == 'pores')    				$pores = str_replace("'", "''", $value);
			elseif($key == 'acne')    				$acne = str_replace("'", "''", $value);
			elseif($key == 'sensitivity')    		$sensitivity = str_replace("'", "''", $value);
			elseif($key == 'briliance')    			$briliance = str_replace("'", "''", $value); 
			elseif($key == 'protection')    		$protection = str_replace("'", "''", $value);
			elseif($key == 'skin_clarity')    		$skin_clarity = str_replace("'", "''", $value);
			elseif($key == 'anti_aging')    		$anti_aging = str_replace("'", "''", $value);
			elseif($key == 'skintone')    			$skintone = str_replace("'", "''", $value);
			elseif($key == 'nourishing')    		$nourishing = str_replace("'", "''", $value);
			elseif($key == 'anti_dark_circle')    	$anti_dark_circle = str_replace("'", "''", $value);
			elseif($key == 'anti_puffiness')    	$anti_puffiness = str_replace("'", "''", $value);
		}
		
		$checkFlag = false;
		$checkFlag2 = false;
		$checkFlag3 = false;
		$checkFlag4 = false;
		$checkFlag5 = false;
		$checkFlag6 = false;
		$checkFlag7 = false;
		$checkFlag8 = false;
		$checkFlag9 = false;
		$checkFlagx = false;
		$checkFlag11 = false;
		$checkFlag12 = false;
		$checkFlag13 = false;
		$checkFlag14 = false;
		$lastIDProduct = "";
		$dbLocal = getdbhimirrorqa();
		
		global $dbnamelocal;
		//1. Insert First Batch to USRChannelProducts
		try{
			$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProducts'");
			$stmtgetProduct->execute();	
			$result= $stmtgetProduct->fetchAll();	
			$keylist="";
			$valuelist="";

			foreach ($jsonbody as $key => $value){
				$val=getInsertQueryProduct($key,$value,$result);
					if(count($val)!=0){
						if(empty($keylist)){
							$keylist.=$val['key'];
							$valuelist.=$val['value'];
						}else{
							$keylist.=",".$val['key'];
							$valuelist.=",".$val['value'];
						}
					}
			}

			$insertProduct="insert into USRChannelProducts ($keylist) VALUES ($valuelist)";
			$stmtInsert = $dbLocal->prepare($insertProduct);
			$stmtInsert->execute();
			$stmtLastInsertID = $dbLocal->prepare("SELECT id from USRChannelProducts ORDER by create_time DESC LIMIT 1");
			$stmtLastInsertID->execute();
			$getLastIDresult = $stmtLastInsertID->fetchAll();
			$lastIDProduct = $getLastIDresult[0]['id'];
			$checkFlag = true;
		}catch(PDOException $e){
			echo '{"response":"PDO Exception","errcode":"b2ber003-585"}'.$insertProduct;
			$checkFlag = false;
		}

		//2. Insert to USRChannelProductCategory
		if($checkFlag){
			try{
				$stmtgetCategory = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCategory'");
				$stmtgetCategory->execute();	
				$result2= $stmtgetCategory->fetchAll();
				$keylist2="";
				$valuelist2="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result2);
						if(count($val)!=0){
							if(empty($keylist2)){
								$keylist2.=$val['key'];
								$valuelist2.=$val['value'];
							}else{
								$keylist2.=",".$val['key'];
								$valuelist2.=",".$val['value'];
							}
						}
				}

				$insertCategory ="insert into USRChannelProductCategory (product_id,$keylist2) VALUES ('$lastIDProduct',$valuelist2)";
				$stmtInsert2 = $dbLocal->prepare($insertCategory);
				$stmtInsert2->execute();
				//echo "Success 2";
				$checkFlag2 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-617"}';
				$checkFlag2 = false;		
			}
		}else{
			echo "failed 672\n";
		}

		//3. Insert to USRChannelProductCustomerAge
		if($checkFlag2){
			try{
				$stmtgetAge = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerAge'");
				$stmtgetAge->execute();	
				$result3= $stmtgetAge->fetchAll();
				$ageArray = array();
				$keylist3="";
				$valuelist3="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result3);
						if(count($val)!=0){
							if(empty($keylist3)){
								 $keylist3.=$val['key'];
								 if(is_array($value)){
								 	if(!empty($value)){
								 		foreach($value as $innerKey => $innerValue){
								 	 		$ageArray[] = $innerValue;	
								 	 	}
								 	}else{
								 		$valuelist3.= "'".checkEmpty($val['value'])."'";
								 	}
								 }else{
									$valuelist3.= "'".checkEmpty($val['value'])."'"; 	
								 }
							}else{
								$keylist3.=",".$val['key'];
								$valuelist3.=",".$val['value'];
							}
						}
				}

				if(empty($ageArray)){
					$insertAge ="insert into USRChannelProductCustomerAge (product_id,$keylist3) VALUES ('$lastIDProduct',$valuelist3)";
					$stmtInsert3 = $dbLocal->prepare($insertAge);
					$stmtInsert3->execute();
				}else{
					for($i=0; $i<count($ageArray);$i++){
						$insertAge ="insert into USRChannelProductCustomerAge (product_id,$keylist3) VALUES ('$lastIDProduct','".$ageArray[$i]."'".$valuelist3.")";
						$stmtInsert3 = $dbLocal->prepare($insertAge);
						$stmtInsert3->execute();
					}
				}
				
				//echo "success 3";
				$checkFlag3 = true;
			}catch(PDOException $e){	
				echo '{"response":"PDO Exception","errcode":"b2ber003-672"}';
				$checkFlag3 = false;	
			}
		}else{
			echo "failed 698\n";
		}

		//4. Insert to USRChannelProductCustomerBeautyRitual
		if($checkFlag3){
			try{
				$stmtgetRitual = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerBeautyRitual'");
				$stmtgetRitual->execute();	
				$result4= $stmtgetRitual->fetchAll();	
				$ritualArray = array();
				$keylist4="";
				$valuelist4="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result4);
						if(count($val)!=0){
							if(empty($keylist4)){
								 $keylist4.=$val['key'];
								 if(is_array($value)){
								 	if(!empty($value)){
								 		foreach($value as $innerKey => $innerValue){
								 	 		$ritualArray[] = $innerValue;	
								 	 	}
								 	}else{
								 		$valuelist4.= "'".checkEmpty($val['value'])."'";
								 	}
								 }else{
									$valuelist4.= "'".checkEmpty($val['value'])."'"; 	
								 }
							}else{
								$keylist4.=",".$val['key'];
								$valuelist4.=",".$val['value'];
							}
						}
				}

				if(!empty($ritualArray)){
					for($i=0; $i<count($ritualArray);$i++){
						$insertRitual ="insert into USRChannelProductCustomerBeautyRitual (product_id,$keylist4) VALUES ('$lastIDProduct','".$ritualArray[$i]."'".$valuelist4.")";
						$stmtInsert4 = $dbLocal->prepare($insertRitual);
						$stmtInsert4->execute();
					}
				}else{
					$insertRitual ="insert into USRChannelProductCustomerBeautyRitual (product_id,$keylist4) VALUES ('$lastIDProduct',$valuelist4)";
					$stmtInsert4 = $dbLocal->prepare($insertRitual);
					$stmtInsert4->execute();
				}
				
					//echo "success 4";
					$checkFlag4 = true;	
			}catch(PDOException $e){	
				echo '{"response":"PDO Exception","errcode":"b2ber003-727"}';
				$checkFlag4 = false;	
			}
		}else{
			echo "failed 724\n";
		}

		//Insert Items to USRChannelProductCustomerClass
		if($checkFlag4){
			try{
				$stmtgetClass = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerClass'");
				$stmtgetClass->execute();	
				$result5= $stmtgetClass->fetchAll();
				$keylist5="";
				$valuelist5="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result5);
						if(count($val)!=0){
							if(empty($keylist5)){
								$keylist5.=$val['key'];
								$valuelist5.=$val['value'];
							}else{
								$keylist5.=",".$val['key'];
								$valuelist5.=",".$val['value'];
							}
						}
				}

				$insertClass ="insert into USRChannelProductCustomerClass (product_id,$keylist5) VALUES ('$lastIDProduct',$valuelist5)";
				$stmtInsert5 = $dbLocal->prepare($insertClass);
				$stmtInsert5->execute();
				//echo "success 5";
				$checkFlag5 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-762"}';
				$checkFlag5 = false;
			}
		}else{
			echo "failed 760\n";
		}
		
		//Product Concern
		if($checkFlag5){
			try{
				insertProductConcerns($lastIDProduct, 'wrinkles', $wrinkles, $dbLocal);
				insertProductConcerns($lastIDProduct, 'darkspot', $darkspot, $dbLocal);
				insertProductConcerns($lastIDProduct, 'hydration', $hydration, $dbLocal);
				insertProductConcerns($lastIDProduct, 'pores', $pores, $dbLocal);
				insertProductConcerns($lastIDProduct, 'acne', $acne, $dbLocal);
				insertProductConcerns($lastIDProduct, 'sensitivity', $sensitivity, $dbLocal);
				insertProductConcerns($lastIDProduct, 'briliance', $briliance, $dbLocal);
				insertProductConcerns($lastIDProduct, 'protection', $protection, $dbLocal);
				insertProductConcerns($lastIDProduct, 'skin_clarity', $skin_clarity, $dbLocal);
				insertProductConcerns($lastIDProduct, 'anti_aging', $anti_aging, $dbLocal);
				insertProductConcerns($lastIDProduct, 'skintone', $skintone, $dbLocal);
				insertProductConcerns($lastIDProduct, 'nourishing', $nourishing, $dbLocal);
				insertProductConcerns($lastIDProduct, 'anti_dark_circle', $anti_dark_circle, $dbLocal);
				insertProductConcerns($lastIDProduct, 'anti_puffiness', $anti_puffiness, $dbLocal);
				$checkFlag6 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-788"}';
				$checkFlag6 = false;
			}
		}else{
			echo "failed 786\n";
		}

		// Insert Items to USRChannelProductPrice
		if($checkFlag6){
			try{
				$stmtgetPrice = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPrice'");
				$stmtgetPrice->execute();	
				$result6= $stmtgetPrice->fetchAll();
				$keylist6="";
				$valuelist6="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result6);
						if(count($val)!=0){
							if(empty($keylist6)){
								$keylist6.=$val['key'];
								$valuelist6.=$val['value'];
							}else{
								$keylist6.=",".$val['key'];
								$valuelist6.=",".$val['value'];
							}
						}
				}

				$insertPrice ="insert into USRChannelProductPrice (product_id,$keylist6) VALUES ('$lastIDProduct',$valuelist6)";
				$stmtInsert6 = $dbLocal->prepare($insertPrice);
				$stmtInsert6->execute();
				//echo "success 6";
				$checkFlag7 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-823"}';
				$checkFlag7 = false;
			}
		}else{
			echo "failed 823\n";
		}

		// Insert Items to USRChannelProductsBarcode
		if($checkFlag7){
			try{
				$stmtgetBarcode = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsBarcode'");
				$stmtgetBarcode->execute();	
				$result7= $stmtgetBarcode->fetchAll();
				$keylist7="";
				$valuelist7="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result7);
						if(count($val)!=0){
							if(empty($keylist7)){
								$keylist7.=$val['key'];
								$valuelist7.=$val['value'];
							}else{
								$keylist7.=",".$val['key'];
								$valuelist7.=",".$val['value'];
							}
						}
				}

				$insertBarcode ="insert into USRChannelProductsBarcode (product_id,$keylist7) VALUES ('$lastIDProduct',$valuelist7)";
				$stmtInsert7 = $dbLocal->prepare($insertBarcode);
				$stmtInsert7->execute();
				//echo "Success 7";
				$checkFlag8 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-858"}';
				$checkFlag8 = false;
			}
		}else{
			echo "Failed 858\n";
		}

		//13. Insert items to USRChannelProductCustomerSkintype
		if($checkFlag8){
			try{
				$stmtgetSkin = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerSkintype'");
				$stmtgetSkin->execute();	
				$result8= $stmtgetSkin->fetchAll();
				$skinArray = array();
				$keylist8="";
				$valuelist8="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result8);
						if(count($val)!=0){
							if(empty($keylist8)){
								 $keylist8.=$val['key'];
								 if(is_array($value)){
								 	if(!empty($value)){
								 		foreach($value as $innerKey => $innerValue){
								 	 		$skinArray[] = $innerValue;	
								 	 	}
								 	}else{
								 		$valuelist8.= "'".checkEmpty($val['value'])."'";
								 	}
								 }else{
									$valuelist8.= "'".checkEmpty($val['value'])."'"; 	
								 }
							}else{
								$keylist8.=",".$val['key'];
								$valuelist8.=",".$val['value'];
							}
						}
				}

				if(!empty($skinArray)){
					for($i=0; $i<count($skinArray);$i++){
						$insertSkin ="insert into USRChannelProductCustomerSkintype (product_id,$keylist8) VALUES ('$lastIDProduct','".$skinArray[$i]."'".$valuelist8.")";
						$stmtInsert8 = $dbLocal->prepare($insertSkin);
						$stmtInsert8->execute();
					}
				}else{
					$insertSkin ="insert into USRChannelProductCustomerSkintype (product_id,$keylist8) VALUES ('$lastIDProduct',$valuelist8)";
					$stmtInsert8 = $dbLocal->prepare($insertSkin);
					$stmtInsert8->execute();
				}
				
				//echo "Success 8";
				$checkFlag9 = true;

			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-914"}';
				$checkFlag9 = false;
			}
		}else{
			echo "Failed 884\n";
		}

		//14. Insert Items to USRChannelProductsPurchaseInfo
		if($checkFlag9){
			try{
				$stmtgetPurchase = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsPurchaseInfo'");
				$stmtgetPurchase->execute();	
				$result9= $stmtgetPurchase->fetchAll();	
				$keylist9="";
				$valuelist9="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result9);
						if(count($val)!=0){
							if(empty($keylist9)){
								$keylist9.=$val['key'];
								$valuelist9.=$val['value'];
							}else{
								$keylist9.=",".$val['key'];
								$valuelist9.=",".$val['value'];
							}
						}
				}

				$insertPurchase ="insert into USRChannelProductsPurchaseInfo (product_id,$keylist9) VALUES ('$lastIDProduct',$valuelist9)";
				$stmtInsert9 = $dbLocal->prepare($insertPurchase);
				$stmtInsert9->execute();
				//echo "Success 9";
				$checkFlagx = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-949"}';
				$checkFlagx = false;
			}
		}else{
			echo "Failed 920\n";
		}

		//15. Insert items to USRChannelProductsSize
		if($checkFlagx){
			try{
				$stmtgetSize = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsSize'");
				$stmtgetSize->execute();	
				$resultx= $stmtgetSize->fetchAll();	
				$keylistx="";
				$valuelistx="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$resultx);
						if(count($val)!=0){
							if(empty($keylistx)){
								$keylistx.=$val['key'];
								$valuelistx.=$val['value'];
							}else{
								$keylistx.=",".$val['key'];
								$valuelistx.=",".$val['value'];
							}
						}
				}

				$insertSize ="insert into USRChannelProductsSize (product_id,$keylistx) VALUES ('$lastIDProduct',$valuelistx)";
				$stmtInsertx = $dbLocal->prepare($insertSize);
				$stmtInsertx->execute();
				//echo "Success 10";
				$checkFlag11 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-984"}';
				$checkFlag11 = false;
			}
		}else{
			echo "Failed 956\n";
		}

		// //Product Photo USRChannelProductPhoto
		$countr=0;
		$photoUploadLog = "";
		if($checkFlag11){
			try{
				$countphoto=count($photo_link);

				$stmtgetPhoto = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPhoto'");
				$stmtgetPhoto->execute();	
				$result11= $stmtgetPhoto->fetchAll();
				$photoLinkArray = array();
				$s3pathArray = array();
				$extensionArray = array();
				$keylist11="";
				$valuelist11="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result11);
						if(count($val)!=0){
							if(empty($keylist11)){
								 $keylist11.=$val['key'];
								 if(is_array($value)){
								 	 foreach($value as $innerKey => $innerValue){
								 	 	$photoLinkArray[] = $innerValue;	
								 	 }
								 }else{
									$valuelist11.=$val['value']; 	
								 }
							}else{
								if(is_array($value)){
									foreach($value as $innerKey => $innerValue){
										$rand = rand(0,999);
										$time = time();
										$extension = explode(".", $innerValue);
										$extension = strtolower(end($extension));
										$hashvalue=hash('sha256',php_uname('n').$time.$rand);
										$s3path =  "$channel_name/Product/".$hashvalue.".".$extension;
										$filename = basename($innerValue);
										$filepath = $ProductPhotoFilepath.$filename;
										if(file_exists($filepath)){																					
											$s3pathArray[] = $s3path;
									 		$extensionArray[] = $extension;
									 		$photoLinkArray[] = uploadtoS3($filepath,$s3path);								 	 	
										}else{											
											$filepath = 'N/A';
											$s3pathArray[] = 'N/A';
									 		$extensionArray[] = 'N/A';
									 		$photoLinkArray[] = 'N/A';
										}
								 	 	
								 	 }
								}else{
									$keylist11.=",".$val['key'];
									$valuelist11.=",".$val['value']; 	
								 }
							}
						}
				}

				for($i=0; $i<count($photoLinkArray);$i++){
					$insertPhoto ="insert into USRChannelProductPhoto (product_id,photo_type,photo_link,s3_path,$keylist11) VALUES ('$lastIDProduct','$extensionArray[$i]', '$photoLinkArray[$i]','$s3pathArray[$i]',  $valuelist11 ) ";
					$stmtInsert11 = $dbLocal->prepare($insertPhoto);
					$stmtInsert11->execute();
					$countr++;
					$checkFlag12 = true;
				}
				$checkFlag12 = true;
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-1059"}';
				$checkFlag12 = false;
			}
		}else{
			echo "Failed 1064\n";
		}

		if($checkFlag12 && $countr>0){
			echo "success";
		}else{
			echo "1070 Failed to upload Photo";
		}
	}
}

function editProd($prodID,$jsonbody,$type, $channel_id){

	$dbLocal = getdbhimirrorqa();
	global $dbnamelocal,$bucketname,$ProductPhotoFilepath;
	$channel_name = "";
	 $selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id = '$channel_id'";
	 $stmt = $dbLocal->prepare($selectchannelname);
	 try {
	  $stmt->execute();
	  $res = $stmt->fetchAll();
	  foreach ($res as $key => $value) {
	   $channel_name = $value['name'];
	  }
	  
	 } catch (PDOException $e) {
	  echo "865".$e;
	 }

	if($type == 'unrestricted'){
		$checkFlag1 = false;
		$checkFlag2 = false;
		$checkQ = "SELECT * from USRChannelUnrestrictedProduct WHERE `id` = '".$prodID."'";
		$stmt = $dbLocal->query($checkQ);
		$stmt->execute();
		$result= $stmt->fetchAll();
		$countDup = count($result);
		global $dbnamelocal;

		if(!empty($countDup)){

			try{
				$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelUnrestrictedProduct'");
				$stmtgetProduct->execute();	
				$result= $stmtgetProduct->fetchAll();	
				$keylist="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result);
						if(count($val)!=0){
							if($val['key'] !='create_time'){
								if(empty($keylist)){
									$keylist.=$val['key']." = ".$val['value'];	
								}else{
									$keylist.=", ".$val['key']." = ".$val['value'];
								}
							}
						}
				}

				$updateProduct="UPDATE USRChannelUnrestrictedProduct SET $keylist,  update_time = CURRENT_TIMESTAMP WHERE id = '$prodID' and is_deleted = 0";
				$stmtupdate = $dbLocal->prepare($updateProduct);
				$stmtupdate->execute();
				$checkFlag1 = true;
			}catch(PDOException $e){
				echo $e->getMessage()."\n Failed 1269 \n";
				$checkFlag1 = false;
			}
			if($checkFlag1){
				try{
					$stmtgetPrice = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPrice'");
					$stmtgetPrice->execute();	
					$result= $stmtgetPrice->fetchAll();	
					$keylist="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result);
							if(count($val)!=0){
									if(empty($keylist)){
										$keylist.=$val['key']." = ".$val['value'];	
									}else{
										$keylist.=",".$val['key']." = ".$val['value'];
									}
							}
					}

					$updatePrice ="UPDATE USRChannelProductPrice SET $keylist, update_time = CURRENT_TIMESTAMP WHERE unrestricted_product_id = '$prodID' and is_deleted = 0";
					$stmtupdatePrice = $dbLocal->prepare($updatePrice);
					$stmtupdatePrice->execute();
					$checkFlag2 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1120 \n";
					$checkFlag2 = false;
				}
			}else{
				echo "Failed 1134 \n";
			}
			if($checkFlag2){
				try{
				$oldphotoArray = array();
				$UpphotoArray = array();
				$dbphoto = array();
				$finalphotoArray = array();

				foreach ($jsonbody['photo_link'] as $value) {
					if(strpos($value, $bucketname)==true){
						array_push($oldphotoArray, $value);
					}else{
						if($value != null)
						array_push($UpphotoArray, $value);						
					}				
				}
				$selectQueryphoto = "SELECT photo_link FROM USRChannelProductPhoto where unrestricted_product_id = '".$prodID."' AND is_deleted = 0";
				$stmt = $dbLocal->prepare($selectQueryphoto);
				if($stmt->execute()){
					$result = $stmt->fetchAll();
					foreach ($result as $value) {
						foreach ($value as $val) {
							array_push($dbphoto,$val);
						}
					}
				}

				$photodelete = array_diff($dbphoto, $oldphotoArray);
					if(!empty($photodelete)){
						foreach ($photodelete as $key => $value) {
							$photovalue = $value;
							$deletePhoto = "UPDATE USRChannelProductPhoto SET is_deleted = 1 , update_time = CURRENT_TIMESTAMP where photo_link = '".$photovalue."'";
							$stmtdel = $dbLocal->prepare($deletePhoto);
							$stmtdel->execute();
						}	
					}
				$countr3 = 0;
				$countphoto=count($UpphotoArray);

				if($countphoto != 0){			
					foreach ($UpphotoArray as $value) {
						$rand = rand(0,999);
						$time = time();
						$extension = explode(".", $value);
						$extension = strtolower(end($extension));
						$hashvalue=hash('sha256',php_uname('n').$time.$rand);
						$s3path =  "$channel_name/Product/".$hashvalue.".".$extension;
						$filename = basename($value);					
						$filepath = $ProductPhotoFilepath."$filename";
						if(file_exists($filepath)){
							$insertQ2 = "INSERT INTO `USRChannelProductPhoto` (`unrestricted_product_id`,`photo_link`,`photo_type`,`s3_path`,`update_time`) VALUES ('".$prodID."','".uploadtoS3($filepath,$s3path)."','".$extension."','".$s3path."',CURRENT_TIMESTAMP)";
							try{
								$stmtinsert = $dbLocal->prepare($insertQ2);	
								if($stmtinsert->execute()){
									$countr3++;
								}else{
									echo "execute error in USRChannelProductPhoto";
								}

							}catch(PDOException $e){
								echo $e->getMessage()." Failed 1277";
							}
						}else{
							$insertQ2 = "INSERT INTO `USRChannelProductPhoto` (`unrestricted_product_id`,`photo_link`,`photo_type`,`s3_path`,`update_time`) VALUES ('".$prodID."','N/A','N/A','N/A',CURRENT_TIMESTAMP)";
							try{
								$stmtinsert = $dbLocal->prepare($insertQ2);	
								if($stmtinsert->execute()){
									$countr3++;
								}else{
									echo "execute error in USRChannelProductPhoto";
								}

							}catch(PDOException $e){
								echo $e->getMessage()." Failed 1277";
							}
						}
						if($countphoto == $countr3){
							echo "success";
						}else{
							echo "failed to upload photo";
						}														
					}
				}
				
				}catch(PDOException $e){
					echo "Failed 1287";
				}
			}else{
				echo "Failed 1748 \n";
			}
		}else{
			echo "$prodID Doesn't exist \n";
		}	

	}elseif($type == 'skincare'){

		$wrinkle = "";
		$darkspot = "";
		$hydration = "";
		$pores = "";
		$acne = "";
		$sensitivity = "";
		$briliance = "";
		$protection = "";
		$skin_clarity = "";
		$anti_aging = "";
		$skintone = "";
		$nourishing = "";
		$anti_dark_circle = "";
		$anti_puffiness = "";
		$photo_link = array();

		foreach($jsonbody as $key => $value){
			if($key == 'wrinkles')            	$wrinkle = str_replace("'", "''", $value);
			elseif($key == 'darkspot')        		$darkspot = str_replace("'", "''", $value);
			elseif($key == 'hydration')       		$hydration = str_replace("'", "''", $value);
			elseif($key == 'pores')          		$pores =  str_replace("'", "''", $value);
			elseif($key == 'acne')            		$acne =    str_replace("'", "''", $value);
			elseif($key == 'sensitivity')     		$sensitivity = str_replace("'", "''", $value);
			elseif($key == 'briliance')       		$briliance = str_replace("'", "''", $value);
			elseif($key == 'protection')      		$protection = str_replace("'", "''", $value);
			elseif($key == 'skin_clarity')    		$skin_clarity = str_replace("'", "''", $value);
			elseif($key == 'anti_aging')      		$anti_aging = str_replace("'", "''", $value);
			elseif($key == 'skintone')        		$skintone = str_replace("'", "''", $value);
			elseif($key == 'nourishing')      		$nourishing = str_replace("'", "''", $value);
			elseif($key == 'anti_dark_circle')		$anti_dark_circle = str_replace("'", "''", $value);
			elseif($key == 'anti_puffiness')  		$anti_puffiness = str_replace("'", "''", $value);
		}

		$checkFlag = false;
		$checkFlag2 = false;
		$checkFlag3 = false;
		$checkFlag4 = false;
		$checkFlag5 = false;
		$checkFlag6 = false;
		$checkFlag7 = false;
		$checkFlag8 = false;
		$checkFlag9 = false;
		$checkFlagx = false;
		$checkFlag11 = false;
		$checkFlag12 = false;
		$checkFlag13 = false;
		$checkFlag14 = false;

		$checkQ = "SELECT * from USRChannelProducts WHERE `id` = '".$prodID."'";
		$stmt = $dbLocal->query($checkQ);
		$stmt->execute();
		$result= $stmt->fetchAll();
		$countDup = count($result);
		global $dbnamelocal;

		if(!empty($countDup)){	

			try{
				$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProducts'");
				$stmtgetProduct->execute();	
				$result= $stmtgetProduct->fetchAll();	
				$keylist="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryProduct($key,$value,$result);
						if(count($val)!=0){
							if($val['key'] !='create_time'){
								if(empty($keylist)){
									$keylist.=$val['key']." = ".$val['value'];	
								}else{
									$keylist.=", ".$val['key']." = ".$val['value'];
								}
							}
						}
				}

				$updateProduct="UPDATE USRChannelProducts SET $keylist WHERE id = '$prodID' and is_deleted = 0";
				$stmtupdate = $dbLocal->prepare($updateProduct);
				$stmtupdate->execute();
				$checkFlag = true;
			}catch(PDOException $e){
				echo $e->getMessage()."\n Failed 1269 \n";
				$checkFlag = false;
			}

			if($checkFlag){
				try{
					$stmtgetCategory = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCategory'");
					$stmtgetCategory->execute();	
					$result2= $stmtgetCategory->fetchAll();	
					$keylist2="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result2);
							if(count($val)!=0){
									if(empty($keylist2)){
										$keylist2.=$val['key']." = ".$val['value'];	
									}else{
										$keylist2.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updateCategory ="UPDATE USRChannelProductCategory SET $keylist2 WHERE product_id = '$prodID' and is_deleted = 0";
					//echo $updateCategory;
					$stmtupdate2 = $dbLocal->prepare($updateCategory);
					$stmtupdate2->execute();
					//echo "Success 2";
					$checkFlag2 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1298 \n";
					$checkFlag2 = false;
				}
			}else{
				echo "Failed 1302 \n";
			}

			if($checkFlag2){
				try{
					$deleteOldValuesQ = "UPDATE USRChannelProductCustomerAge SET is_deleted = 1, update_time = CURRENT_TIMESTAMP WHERE `product_id` = '".$prodID."'";
					$stmtdelAge = $dbLocal->prepare($deleteOldValuesQ);
					$stmtdelAge->execute();

					$stmtgetAge = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerAge'");
					$stmtgetAge->execute();	
					$result3= $stmtgetAge->fetchAll();
					$ageArray = array();
					$keylist3="";
					$valuelist3="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result3);
							if(count($val)!=0){
								if(empty($keylist3)){
									 $keylist3.=$val['key'];
									 if(is_array($value)){
									 	if(!empty($value)){
									 		foreach($value as $innerKey => $innerValue){
									 	 		$ageArray[] = $innerValue;	
									 	 	}
									 	}else{
									 		$valuelist3.= "'".checkEmpty($val['value'])."'";
									 	}
									 }else{
										$valuelist3.= "'".checkEmpty($val['value'])."'"; 	
									 }
								}else{
									$keylist3.=",".$val['key'];
									$valuelist3.=",".$val['value'];
								}
							}
					}

					if(empty($ageArray)){
						$insertAge ="insert into USRChannelProductCustomerAge (product_id,$keylist3) VALUES ('$prodID', $valuelist3)";
						$stmtUpdateAge = $dbLocal->prepare($insertAge);
						$stmtUpdateAge->execute();
					}else{
						for($i=0; $i<count($ageArray);$i++){
							$insertAge ="insert into USRChannelProductCustomerAge (product_id,$keylist3) VALUES ('$prodID','".$ageArray[$i]."'".$valuelist3.")";
							$stmtUpdateAge = $dbLocal->prepare($insertAge);
							$stmtUpdateAge->execute();
						}
					}

					$checkFlag3 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1354 \n";
					$checkFlag3 = false;
				}
			}else{
				echo "Failed 1359 \n";
			}

			if($checkFlag3){
				try{
					$deleteOldValuesQ2 = "UPDATE USRChannelProductCustomerBeautyRitual SET is_deleted = 1, update_time = CURRENT_TIMESTAMP WHERE `product_id` = '".$prodID."'";
					$stmtdelRitual = $dbLocal->prepare($deleteOldValuesQ2);
					$stmtdelRitual->execute();

					foreach($jsonbody['beauty_ritual'] as $ritual_value){
						$updateNewValuesQ2 = $dbLocal->prepare("INSERT INTO USRChannelProductCustomerBeautyRitual (product_id, beauty_ritual, update_time) VALUES ('$prodID', '$ritual_value', CURRENT_TIMESTAMP)");
						$updateNewValuesQ2->execute();
					}

					$checkFlag4 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1090 \n";
					$checkFlag4 = false;
				}
			}else{
				echo "Failed 1379 \n";
			}

			if($checkFlag4){
				try{
					$stmtgetClass = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerClass'");
					$stmtgetClass->execute();	
					$result4= $stmtgetClass->fetchAll();	
					$keylist4="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result4);
							if(count($val)!=0){
									if(empty($keylist4)){
										$keylist4.=$val['key']." = ".$val['value'];	
									}else{
										$keylist4.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updateClass ="UPDATE USRChannelProductCustomerClass SET $keylist4 WHERE product_id = '$prodID' and is_deleted = 0";
					$stmtupdateClass = $dbLocal->prepare($updateClass);
					$stmtupdateClass->execute();
					$checkFlag5 = true;
					//echo "Success 1";
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1105 \n";
					$checkFlag5 = false;
				}
			}else{
				echo "Failed 1410 \n";
			}

			if($checkFlag5){
				try{
					$stmtgetPrice = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductPrice'");
					$stmtgetPrice->execute();	
					$result5= $stmtgetPrice->fetchAll();	
					$keylist5="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result5);
							if(count($val)!=0){
									if(empty($keylist5)){
										$keylist5.=$val['key']." = ".$val['value'];	
									}else{
										$keylist5.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updatePrice ="UPDATE USRChannelProductPrice SET $keylist5 WHERE product_id = '$prodID' and is_deleted = 0";
					$stmtupdatePrice = $dbLocal->prepare($updatePrice);
					$stmtupdatePrice->execute();
					$checkFlag7 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1120 \n";
					$checkFlag7 = false;
				}
			}else{
				echo "Failed 1440 \n";
			}

			if($checkFlag7){
				try{
					$stmtgetBarcode = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsBarcode'");
					$stmtgetBarcode->execute();	
					$result6= $stmtgetBarcode->fetchAll();	
					$keylist6="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result6);
							if(count($val)!=0){
									if(empty($keylist6)){
										$keylist6.=$val['key']." = ".$val['value'];	
									}else{
										$keylist6.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updatebarcode ="UPDATE USRChannelProductsBarcode SET $keylist6 WHERE product_id = '$prodID' and is_deleted = 0";
					$stmtupdatebarcode = $dbLocal->prepare($updatebarcode);
					$stmtupdatebarcode->execute();
					$checkFlag8 = true;
					// echo "Success 3";
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1135 \n";
					$checkFlag8 = false;
				}
			}else{
				echo "Failed 1471 \n";
			}

			if($checkFlag8){
				try{
					$deleteOldValuesQ3 = "UPDATE USRChannelProductCustomerSkintype SET is_deleted = 1, update_time = CURRENT_TIMESTAMP WHERE `product_id` = '".$prodID."'";
					$stmtdelSkinType = $dbLocal->prepare($deleteOldValuesQ3);
					$stmtdelSkinType->execute();

					$stmtgetSkin = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerSkintype'");
					$stmtgetSkin->execute();	
					$result7= $stmtgetSkin->fetchAll();
					$skinArray = array();
					$keylist7="";
					$valuelist7="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result7);
							if(count($val)!=0){
								if(empty($keylist7)){
									 $keylist7.=$val['key'];
									 if(is_array($value)){
									 	if(!empty($value)){
									 		foreach($value as $innerKey => $innerValue){
									 	 		$skinArray[] = $innerValue;	
									 	 	}
									 	}else{
									 		$valuelist7.= "'".checkEmpty($val['value'])."'";
									 	}
									 }else{
										$valuelist7.= "'".checkEmpty($val['value'])."'"; 	
									 }
								}else{
									$keylist7.=",".$val['key'];
									$valuelist7.=",".$val['value'];
								}
							}
					}

					if(empty($skinArray)){
						$insertAge ="insert into USRChannelProductCustomerSkintype (product_id,$keylist7) VALUES ('$prodID', $valuelist7)";
						$stmtUpdateAge = $dbLocal->prepare($insertAge);
						$stmtUpdateAge->execute();
					}else{
						for($i=0; $i<count($skinArray);$i++){
							$insertAge ="insert into USRChannelProductCustomerSkintype (product_id,$keylist7) VALUES ('$prodID','".$skinArray[$i]."'".$valuelist7.")";
							$stmtUpdateAge = $dbLocal->prepare($insertAge);
							$stmtUpdateAge->execute();
						}
					}
					
					$checkFlag9 = true;
					// echo "Success 4";
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1156 \n";
					$checkFlag9 = false;
				}
			}else{
				echo "Failed 1529 \n";
			}

			if($checkFlag9){
				try{
					$stmtgetPurchase = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsPurchaseInfo'");
					$stmtgetPurchase->execute();	
					$result8= $stmtgetPurchase->fetchAll();	
					$keylist8="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result8);
							if(count($val)!=0){
									if(empty($keylist8)){
										$keylist8.=$val['key']." = ".$val['value'];	
									}else{
										$keylist8.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updatePurchase ="UPDATE USRChannelProductsPurchaseInfo SET $keylist8 WHERE product_id = '$prodID' and is_deleted = 0";
					$stmtupdatePurchase = $dbLocal->prepare($updatePurchase);
					$stmtupdatePurchase->execute();
					$checkFlagx = true;
					// echo "Success 5";
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1171 \n";
					$checkFlagx = false;
				}
			}else{
				echo "Failed 1560 \n";
			}

			if($checkFlagx){
				try{
					$stmtgetSize = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductsSize'");
					$stmtgetSize->execute();	
					$result9= $stmtgetSize->fetchAll();	
					$keylist9="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$result9);
							if(count($val)!=0){
									if(empty($keylist9)){
										$keylist9.=$val['key']." = ".$val['value'];	
									}else{
										$keylist9.=", ".$val['key']." = ".$val['value'];
									}
							}
					}

					$updateSize ="UPDATE USRChannelProductsSize SET $keylist9 WHERE product_id = '$prodID' and is_deleted = 0";
					$stmtupdateSize = $dbLocal->prepare($updateSize);
					$stmtupdateSize->execute();
					$checkFlag11 = true;
					//echo "Success 7";
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1186 \n";
					$checkFlag11 = false;
				}
			}else{
				echo "Failed 1591 \n";
			}

			if($checkFlag11){
				try{
					updateProductConcern($prodID, 'wrinkles', $wrinkle, $dbLocal);
					updateProductConcern($prodID, 'darkspot', $darkspot, $dbLocal);
					updateProductConcern($prodID, 'hydration', $hydration, $dbLocal);
					updateProductConcern($prodID, 'pores', $pores, $dbLocal);
					updateProductConcern($prodID, 'acne', $acne, $dbLocal);
					updateProductConcern($prodID, 'sensitivity', $sensitivity, $dbLocal);
					updateProductConcern($prodID, 'briliance', $briliance, $dbLocal);
					updateProductConcern($prodID, 'protection', $protection, $dbLocal);
					updateProductConcern($prodID, 'skin_clarity', $skin_clarity, $dbLocal);
					updateProductConcern($prodID, 'anti_aging', $anti_aging, $dbLocal);
					updateProductConcern($prodID, 'skintone', $skintone, $dbLocal);
					updateProductConcern($prodID, 'nourishing', $nourishing, $dbLocal);
					updateProductConcern($prodID, 'anti_dark_circle', $anti_dark_circle, $dbLocal);
					updateProductConcern($prodID, 'anti_puffiness', $anti_puffiness, $dbLocal);
					//echo "Success 8";
					$checkFlag12 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1212 \n";
					$checkFlag12 = false;
				}
			}else{
				echo "Failed 1617 \n";
			}

			if($checkFlag12){
				try{
					$deleteOldValuesQ4 = "UPDATE USRChannelProductCustomerBeautyRitual SET is_deleted = 1, update_time = CURRENT_TIMESTAMP WHERE `product_id` = '".$prodID."'";
					$stmtdelRitual = $dbLocal->prepare($deleteOldValuesQ4);
					$stmtdelRitual->execute();

					$stmtgetRitual = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRChannelProductCustomerBeautyRitual'");
					$stmtgetRitual->execute();	
					$resultx= $stmtgetRitual->fetchAll();
					$ritualArray = array();
					$keylistx="";
					$valuelistx="";

					foreach ($jsonbody as $key => $value){
						$val=getInsertQueryProduct($key,$value,$resultx);
							if(count($val)!=0){
								if(empty($keylistx)){
									 $keylistx.=$val['key'];
									 if(is_array($value)){
									 	if(!empty($value)){
									 		foreach($value as $innerKey => $innerValue){
									 	 		$ritualArray[] = $innerValue;	
									 	 	}
									 	}else{
									 		$valuelistx.= "'".checkEmpty($val['value'])."'";
									 	}
									 }else{
										$valuelistx.= "'".checkEmpty($val['value'])."'"; 	
									 }
								}else{
									$keylistx.=",".$val['key'];
									$valuelistx.=",".$val['value'];
								}
							}
					}

					if(empty($ritualArray)){
						$insertRitual ="insert into USRChannelProductCustomerBeautyRitual (product_id,$keylistx) VALUES ('$prodID', $valuelistx)";
						$stmtUpdateRitual = $dbLocal->prepare($insertRitual);
						$stmtUpdateRitual->execute();
					}else{
						for($i=0; $i<count($ritualArray);$i++){
							$insertRitual ="insert into USRChannelProductCustomerBeautyRitual (product_id,$keylistx) VALUES ('$prodID','".$ritualArray[$i]."'".$valuelistx.")";
							$stmtUpdateRitual = $dbLocal->prepare($insertRitual);
							$stmtUpdateRitual->execute();
						}
					}
					// echo "Success 8";
					$checkFlag13 = true;
				}catch(PDOException $e){
					echo $e->getMessage()."\n Failed 1212 \n";
					$checkFlag13 = false;
				}
			}else{
				echo "Failed 1674";
			}

			if($checkFlag13){
				try{
				$oldphotoArray = array();
				$UpphotoArray = array();
				$dbphoto = array();
				$finalphotoArray = array();

				foreach ($jsonbody['photo_link'] as $value) {
					if(strpos($value, $bucketname)==true){
						array_push($oldphotoArray, $value);
					}else{					
						if($value!=null)
						array_push($UpphotoArray, $value);
					}				
				}
				$selectQueryphoto = "SELECT photo_link FROM USRChannelProductPhoto where product_id = '".$prodID."' AND is_deleted = 0";
				$stmt = $dbLocal->prepare($selectQueryphoto);
				if($stmt->execute()){
					$result = $stmt->fetchAll();
					foreach ($result as $value) {
						foreach ($value as $val) {
							array_push($dbphoto,$val);
						}
					}
				}

				$photodelete = array_diff($dbphoto, $oldphotoArray);
					if(!empty($photodelete)){
						foreach ($photodelete as $key => $value) {
							$photovalue = $value;
							$deletePhoto = "UPDATE USRChannelProductPhoto SET is_deleted = 1 , update_time = CURRENT_TIMESTAMP where photo_link = '".$photovalue."'";
							$stmtdel = $dbLocal->prepare($deletePhoto);
							$stmtdel->execute();
						}	
					}

				$countr3 = 0;
				$countphoto=count($UpphotoArray);

				if($countphoto != 0){			
					foreach ($UpphotoArray as $value) {
						$rand = rand(0,999);
						$time = time();
						$extension = explode(".", $value);
						$extension = strtolower(end($extension));
						$hashvalue=hash('sha256',php_uname('n').$time.$rand);
						$s3path =  "$channel_name/Product/".$hashvalue.".".$extension;
						$filename = basename($value);						
						$filepath = $ProductPhotoFilepath."$filename";
						if(file_exists($filepath)){
							$insertQ2 = "INSERT INTO `USRChannelProductPhoto` (`product_id`,`photo_link`,`photo_type`,`s3_path`,`update_time`) VALUES ('".$prodID."','".uploadtoS3($filepath,$s3path)."','".$extension."','".$s3path."',CURRENT_TIMESTAMP)";							
							try{
								$stmtinsert = $dbLocal->prepare($insertQ2);	
								if($stmtinsert->execute()){
									$countr3++;
								}else{
									echo "execute error in USRChannelProductPhoto";
								}

							}catch(PDOException $e){
								echo $e->getMessage()." Failed 1277";
							}
						}else{
							$insertQ2 = "INSERT INTO `USRChannelProductPhoto` (`product_id`,`photo_link`,`photo_type`,`s3_path`,`update_time`) VALUES ('".$prodID."','N/A','N/A','N/A',CURRENT_TIMESTAMP)";							
							try{
								$stmtinsert = $dbLocal->prepare($insertQ2);	
								if($stmtinsert->execute()){
									$countr3++;
								}else{
									echo "execute error in USRChannelProductPhoto";
								}

							}catch(PDOException $e){
								echo $e->getMessage()." Failed 1277";
							}
						}															
					}
				}
				if($countphoto == $countr3){
					echo "success";
				}else{
					echo "failed to upload photo";
				}
				}catch(PDOException $e){
					echo "Failed 1287";
				}
			}else{
				echo "Failed 1748 \n";
			} 
		}else{
			echo "$prodID Doesn't exist \n";
		}
	}
}


function deleteProd($prodID, $type){
	
	$dbLocal = getdbhimirrorqa();
	
	if($type == 'unrestricted'){
		$checkQ = "SELECT * from `USRChannelUnrestrictedProduct` WHERE `id` = '".$prodID."'";
		$stmt = $dbLocal->query($checkQ);
		$stmt->execute();
		$result= $stmt->fetchAll();
		$numofdata = $stmt->rowCount();
		if($numofdata > 0){
			$delQuery = "UPDATE `USRChannelUnrestrictedProduct` set is_deleted = '1' WHERE `USRChannelUnrestrictedProduct`.`id` = '".$prodID."'";
			try{
				$stmtdel = $dbLocal->query($delQuery);
				if($stmtdel->execute()){					
					echo "success";
				}else
					echo "Failed id $prodID doesn't exist";
			}catch(PDOException $e){
				echo '{"response":"failed","errcode":"b2ber1212","message":"failed to delete unrestricted product"}';
			}
		}else{
			echo "$prodID doesn't exist in the database";
		}
	}elseif($type == 'skincare'){
		$checkQ = "SELECT * from USRChannelProducts WHERE `id` = '".$prodID."'";
		$stmt = $dbLocal->query($checkQ);
		$stmt->execute();
		$result= $stmt->fetchAll();
		$countDup = count($result);

		try{
			if(!empty($countDup)){
				$deleteQ = "UPDATE USRChannelProducts SET is_deleted = 1 WHERE `id` = '".$prodID."'";
				$stmtdelete = $dbLocal->prepare($deleteQ);
				if($stmtdelete->execute()){
					echo "Successfully deleted";
				}else{
					echo "failed to delete";
				}
			}else{
				echo "no records found for prod_id $prodID";
			}
		}catch(PDOException $e){
			echo '{"response":"failed","errcode":"b2ber1213","message":"failed to delete skincare product"}';
		}
	}else{
		echo "invalid type";
	}
}

function GetProductFilterCategory(){
	try{
		$dbLocal = getdbhimirrorqa();
		$prodListArr = array();
		$stmtget = $dbLocal->prepare("SELECT DISTINCT type FROM B2BSkinCareProduct");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		$prodListArr['type']=$result;
		$stmtget = $dbLocal->prepare("SELECT DISTINCT currency FROM B2BSkinCareProduct");
		$stmtget->execute();	
		$result2= $stmtget->fetchAll();
		$prodListArr['currency']=$result2;
		$stmtget = $dbLocal->prepare("SELECT DISTINCT brand_name FROM B2BSkinCareProduct");
		$stmtget->execute();	
		$result3= $stmtget->fetchAll();
		$prodListArr['brand_name']=$result3;
		echo json_encode($prodListArr);
	}
	catch(PDOException $e){
		echo "message: ".$e->getMessage();
	}
}

function createprodunrestricted($body){

	$type = "";
	$brand = "";
	$currency = "";
	$activation = "";
	$product_name = "";
	$price = "";
	$photo_link = "";
	$prod_desc = "";
	$photo_type = "";
	$s3_path = "";
	$ranking = "";
	foreach ($body as $key => $value) {
		switch ($key) {
			case 'brand_name': //
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$brand = $value;
				}else{
					$brand = "N/A";
				}
				break;
			case 'currency':
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$currency = $value;
				}else{
					$currency = "N/A";
				}
				break;
			case 'activation':
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$activation = $value;
				}else{
					$activation = "N/A";
				}
				break;
			case 'product_name': //
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$product_name = $value;
				}else{
					$product_name = "N/A";
				}
				break;
			case 'price':
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$price = $value;
				}else{
					$price = "N/A";
				}
				break;
			case 'photo_link'://
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$photo_link = $value;
				}else{
					$photo_link = "N/A";
				}
				break;
			case 'product_desc'://
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$prod_desc = $value;
				}else{
					$prod_desc = "N/A";
				}
				break;
			case 'photo_type'://
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$photo_type = $value;
				}else{
					$photo_type = "N/A";
				}
				break;
			case 's3_path'://
				$value = str_replace("'", "''", $value);
				if(!empty($value)){
					$s3_path = $value;
				}else{
					$s3_path = "N/A";
				}
				break;			
			default:
				# code...
				break;
		}
	}
	$dbLocal = getdbhimirrorqa();	
	$date = date("Y-m-d h:i:s");
	$insertQ = "INSERT INTO `USRChannelUnrestrictedProduct` (`brand`,`name`,`instruction`,`insert_time`) VALUES ('".$brand."','".$product_name."','".$prod_desc."','".$date."')";	
	$selectQuery = "SELECT id FROM USRChannelUnrestrictedProduct ORDER BY id DESC LIMIT 1";
	$insertQ2 = "INSERT INTO `USRChannelProductPhoto` (`unrestricted_product_id`,`photo_link`,`photo_type`,`s3_path`) VALUES (";
	$stmtinsert = $dbLocal->prepare($insertQ);
	if($stmtinsert->execute()){
		$stmt = $dbLocal->prepare($selectQuery);
		if($stmt->execute()){
			$res = $stmt->fetchAll();
			$numofdata = $stmt->rowCount();
			if($numofdata > 0){
				foreach ($res as $value) {
					$unrestrictedId = $value['id'];
					$insertQ2 .= "'".$unrestrictedId."','".json_encode($photo_link)."','".$photo_type."','".$s3_path."')";
					$stmtinsert = $dbLocal->prepare($insertQ2);	
					if($stmtinsert->execute()){
						echo "Success";
					}else{
						echo "execute error in USRChannelProductPhoto";
					}

				}
			}else
				echo "no result";
		}else
			echo "execute error in USRChannelUnrestrictedProduct";
	}

}

function getdistinctValue(){

	$dbLocal = getdbhimirrorqa();
	$valueArrFinal = array();

	try{
		$valueArrFinal["product_name"] = getDistinctWorker("USRChannelProducts","product_name");
		$valueArrFinal["brand_name"] =  getDistinctWorker("USRChannelProducts","brand");
		$valueArrFinal["language"] =  getDistinctWorker("USRChannelProducts","lang");
		$valueArrFinal["gender"] =  getDistinctWorker("USRChannelProducts","customer_gender");
		$valueArrFinal["customer_category"] =  getDistinctWorker("USRChannelProducts","customer_category");
		$valueArrFinal["customer_usage"] =  getDistinctWorker("USRChannelProducts","customer_usage");
		$valueArrFinal["customer_diffusion"] =  getDistinctWorker("USRChannelProducts","customer_diffusion");
		$valueArrFinal["age"] =  getDistinctWorker("USRChannelProductCustomerAge","age_value ");
		$valueArrFinal["category_type"] =  getDistinctWorker("USRChannelProductCategory","category_type");
		$valueArrFinal["beauty_ritual"] =  getDistinctWorker("USRChannelProductCustomerBeautyRitual","beauty_ritual");
		$valueArrFinal["class_type"] =  getDistinctWorker("USRChannelProductCustomerClass","class_type");
		$valueArrFinal["photo_type"] =  getDistinctWorker("USRChannelProductPhoto","photo_type");
		$valueArrFinal["product_price"] =  getDistinctWorker("USRChannelProductPrice","prices_dollar_sign");
		$valueArrFinal["barcode"] =  getDistinctWorker("USRChannelProductsBarcode","barcode_type");
		$valueArrFinal["skin_type"] =  getDistinctWorker("USRChannelProductCustomerSkintype","skin_type");
		$valueArrFinal["purchase_type"] =  getDistinctWorker("USRChannelProductsPurchaseInfo","purchase_type");
		$valueArrFinal["size_type"] =  getDistinctWorker("USRChannelProductsSize","size_type");

		echo json_encode($valueArrFinal);
		
	}catch(PDOException $e){
		echo "ERROR MESSAGE:".$e->getMessage();
	}
}

function getDistinctWorker($tableName, $col_name){

	$dbLocal = getdbhimirrorqa();
	$valueArr = array();

	try{
		$getdistinctQ = "SELECT DISTINCT $col_name from $tableName";
		$stmtgetdistinct = $dbLocal->prepare($getdistinctQ);
		$stmtgetdistinct->execute();
		$result = $stmtgetdistinct->fetchAll();
		foreach($result as $value){
			foreach($value as $key => $value2){
				$valueArr[] = $value2;
			}
		}
			return $valueArr;
	}catch(PDOException $e){
		echo "ERROR message".$e->getMessage();
	}
}

function idGenerator($tbl_name){

	$localhost = gethostbyname(0);
	$create_time = time();
	$random_num = rand(0,1000);
	return $localhost.$create_time.$random_num;
}

function updateProductConcern($prodID, $concern_name, $concern_value, $dbLocal){

	$insertQConcern = "UPDATE USRChannelProductCustomerConcerns SET concerns_value = '$concern_value', update_time = CURRENT_TIMESTAMP WHERE product_id = '$prodID' AND concerns_id LIKE '%$concern_name%'";
	$stmtInsertConcern1 = $dbLocal->prepare($insertQConcern);
	$stmtInsertConcern1->execute();
	//echo "success update $concern_name\n";
}

function insertProductConcerns($prodID, $concern_name, $concern_value, $dbLocal){

	$insertQConcern = "INSERT INTO USRChannelProductCustomerConcerns (product_id, concerns_id, concerns_value, update_time) VALUES ('$prodID', '$concern_name', '$concern_value', CURRENT_TIMESTAMP)";
	$stmtInsertConcern1 = $dbLocal->prepare($insertQConcern);
	$stmtInsertConcern1->execute();

}

function getbrandnamelist(){
	$dbLocal = getdbhimirrorqa();
	$selectQuery = "SELECT DISTINCT brand FROM `USRChannelUnrestrictedProduct`";
	$stmt = $dbLocal->prepare($selectQuery);
	if($stmt->execute()){
		$res = $stmt->fetchAll();
		echo json_encode($res);
	}
}

function distinctphoto($query){
	$dbLocal = getdbhimirrorqa();
	$stmt = $dbLocal->prepare($query);
	if($stmt->execute()){
		$res = $stmt->fetchAll();
		
	}
	return ($res);
}

function getdropdownlistproduct($channelid){
	$dbLocal = getdbhimirrorqa();
	$brandarray= array();
	$agearray= array();
	$beautyarray= array();
	$currencyarray= array();
	$skintypearray= array();
	$concernarray= array();
	$gendarray= array();
	$difarray= array();
	$catarray= array();
	$classarray= array();
	$usagearray= array();
	$linearray= array();
	$subclassarray= array();
	$formulationarray= array();
	$selectbrand = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'brand' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectbrand);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $brandval) {
				array_push($brandarray, $brandval);
			}
		}
				
	}
	$selectAge = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'age' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectAge);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $ageval) {
				array_push($agearray, $ageval);
			}
		}
				
	}
	$selectbeauty = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Beauty ritual' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectbeauty);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $beautyval) {
				array_push($beautyarray, $beautyval);
			}
		}
				
	}
	$selectcurrency = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Price Currency' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectcurrency);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $curval) {
				array_push($currencyarray, $curval);
			}
		}
				
	}
	$selectskinType = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Type of Skin' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectskinType);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $skintypeval) {
				array_push($skintypearray, $skintypeval);
			}
		}
				
	}
	$selectcorcerns = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Customer concerns level' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectcorcerns);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $concernval) {
				array_push($concernarray, $concernval);
			}
		}
				
	}
	$selectgender = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'GENDER' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectgender);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $gendereval) {
				array_push($gendarray, $gendereval);
			}
		}
				
	}
	$selectdiffusion = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'diffusion' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectdiffusion);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $difval) {
				array_push($difarray, $difval);
			}
		}
				
	}
	$selectcategory = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Category' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectcategory);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $catval) {
				array_push($catarray, $catval);
			}
		}
				
	}
	$selectclass = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'CLASS' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectclass);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $classval) {
				array_push($classarray, $classval);
			}
		}
				
	}
	$selectusage = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'USAGE' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectusage);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $usageval) {
				array_push($usagearray, $usageval);
			}
		}
				
	}
	$selectline = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'line' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectline);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $lineval) {
				array_push($linearray, $lineval);
			}
		}
				
	}
	$selectsubclass = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'SUB CLASS' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectsubclass);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $subclassval) {
				array_push($subclassarray, $subclassval);
			}
		}
				
	}
	$selectformulation = "SELECT DISTINCT name FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE 'Formulation' AND is_deleted = '0'";
	$stmt = $dbLocal->prepare($selectformulation);
	if($stmt->execute()){
		$resultbrand = $stmt->fetchAll();
		foreach ($resultbrand as $key => $value) {
			foreach ($value as $formulationval) {
				array_push($formulationarray, $formulationval);
			}
		}
				
	}
	echo json_encode(array("brand"=>$brandarray,"age"=>$agearray,"beauty_ritual"=>$beautyarray,"currency"=>$currencyarray,"type_of_skin"=>$skintypearray,"concerns"=>$concernarray,"gender"=>$gendarray,"diffusion"=>$difarray,"category"=>$catarray,"class"=>$classarray,"usage"=>$usagearray,"line"=>$linearray,"Subclass"=>$subclassarray,"formulation"=>$formulationarray));
}

function checkEmpty($value){

	$value = str_replace("'", "''", $value);
	if(!empty($value)){
		$finalValue = $value;
	}else{
		$finalValue = "N/A";
	}

	return $finalValue;
}

function checkEmptyInt($value){

	$value = str_replace("'", "''", $value);
	if(!empty($value)){
		$finalValue = $value;
	}else{
		$finalValue = 0;
	}

	return $finalValue;
}

function getInsertQueryProduct($key,$value,$columns){
	$returnarray= array();
	foreach ($columns as $columnskey => $columnsvalue){
			
			if($columnsvalue['COLUMN_NAME']==$key){
					$returnarray['key']=$key;
					
					if(strpos($columnsvalue['DATA_TYPE'],"int") !== false){
						$returnarray['value']= checkEmptyInt($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"float") !== false){
						$returnarray['value']= checkEmptyInt($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"double") !== false){
						$returnarray['value']= checkEmptyInt($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"var") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty($value)."'";
						else
							$returnarray['value']=$value;
					}else if(strpos($columnsvalue['DATA_TYPE'],"char") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty($value)."'";
						else
							$returnarray['value']=$value;
					}else if(strpos($columnsvalue['DATA_TYPE'],"text") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty($value)."'";
						else
							$returnarray['value']=$value;
					}else if(strpos($columnsvalue['DATA_TYPE'],"date") !== false){
						if(strpos($columnsvalue['COLUMN_NAME'],"create") !== false||strpos($columnsvalue['COLUMN_NAME'],"update") !== false){
							$returnarray['value']="CURRENT_TIMESTAMP";
						}else{
							
							$returnarray['value']="'".$value."'";
						}
						
					}else if(strpos($columnsvalue['DATA_TYPE'],"time") !== false){
						if(strpos($columnsvalue['COLUMN_NAME'],"create") !== false||strpos($columnsvalue['COLUMN_NAME'],"update") !== false){
							$returnarray['value']="CURRENT_TIMESTAMP";
						}else{
							
							$returnarray['value']="'".$value."'";
						}
					}
			}
	}
	return $returnarray;
}

function createnewmeta($metatype,$metaname,$channelid){
	$insertbrand = "INSERT INTO `USRChannelProductMeta`(`syschannelprofiles_id`, `meta`, `name`, `value`, `sort`, `is_deleted`, `insert_time`) VALUES";
	$selectbrand = "SELECT sort FROM USRChannelProductMeta WHERE syschannelprofiles_id = '$channelid' AND meta LIKE '$metatype' AND is_deleted = '0' ORDER BY insert_time DESC";
	$sortval = "";
	$dbLocal = getdbhimirrorqa();
	try {
		$stmtsel = $dbLocal->prepare($selectbrand);
		$stmtsel->execute();
		$res = $stmtsel->fetchAll();
		foreach ($res as $key => $value) {
			$sortval = $value;
		}
	} catch (PDOException $e) {
		
	}
	if(empty($sortval)){
		$sortval = 1;
	}else{
		foreach ($sortval as $key => $value) {
			$sortval = $value+1;
		}
	}
	$insertbrand .= " ('$channelid','$metatype','$metaname','$metaname',$sortval,0,CURRENT_TIMESTAMP)";
	try {
		$stmtinsert = $dbLocal->prepare($insertbrand);
		if($stmtinsert->execute()){
			echo "success";
		}else{
			echo "ERROR 2149";
		}

	} catch (PDOException $e) {
		echo "2153 ERROR".$e;
	}
}
?>