<?php

function getRecommendedList($channelid){
	
	try{
		$dbLocal = getdbhimirrorqa();
		$prodListArr = array("productPackages");
		$stmtget = $dbLocal->prepare("SELECT * FROM USRChannelProductRecommendationsPackages WHERE `syschannelprofiles_id` = '".$channelid."' AND is_deleted!=1");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		
		echo json_encode($result);
	}
	catch(PDOException $e){
		echo "line 15 message: ".$e->getMessage();
	}
}

function getPackageDetails($packageid){

	try{
		$dbLocal = getdbhimirrorqa();
		
		$stmtget = $dbLocal->prepare("SELECT brand,name as productname,USRChannelProductRecommendations.ranking FROM USRChannelProductRecommendations LEFT JOIN USRChannelUnrestrictedProduct on USRChannelProductRecommendations.product_id=USRChannelUnrestrictedProduct.id WHERE `recommendation_packages_id`='$packageid' AND brand !='NULL'
UNION ALL
SELECT brand,product_name as productname,ranking FROM USRChannelProductRecommendations LEFT JOIN USRChannelProducts on USRChannelProductRecommendations.product_id=USRChannelProducts.id WHERE `recommendation_packages_id`='$packageid' AND brand !='NULL'");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();

		echo json_encode($result);
	}
	catch(PDOException $e){
		echo "line 31 message: ".$e->getMessage();
	}
}

function addRecommendedProduct($jsonbody,$channelid){
	global $ProductPhotoFilepath,$bucketname;
	$dbLocal = getdbhimirrorqa();
	$package_name = "";
	$syschannelprofiles_id = $channelid;
	$font_size = 0;
	$font_color = "";
	$applied_page = "";
	$insert_time = "";
	$update_time = "";
	$background_image_url = "";
	$background_image_s3_path = "";
	$activation_status = 0;	

	$unrestricted_order = 0;
	$unrestricted_id = "";
	$sproduct_order = 0;
	$sproduct_id = "";

	$is_deleted = 0;

	$uproduct = array();
	$sproduct = array();

	$s3_check_flag = false;
	$channel_name_flag = false;
	$channelname = "";

	$selectChannelname = "SELECT * FROM `SYSChannelProfiles` WHERE `id` = '".$channelid."'";

	try{
		$stmtChannelName = $dbLocal->prepare($selectChannelname);
		if($stmtChannelName->execute()){
			$res = $stmtChannelName->fetchAll();
			if(count($res)>0){
				$channelname = $res[0]['name'];
				$channel_name_flag = true;
			}
		}
	}catch(PDOException $e){
		echo "error in Line  : -> ".$e->getMessage();
	}

	$dataKeys = "";
	$dataValues = "";

	if($channel_name_flag){
		foreach ($jsonbody as $key => $value) {
			switch ($key) {
				case 'package_name':
					$package_name = str_replace("'", "''", $value);
					$dataKeys .= "`package_name`,";
					$dataValues .= "'".$package_name."',";

					break;
				case 'font_size':
					$font_size = $value;
					$dataKeys .= "`font_size`,";
					$dataValues .= "'".$font_size."',";
					
					break;
				case 'font_color':
					$font_color = str_replace("'", "''", $value);
					$dataKeys .= "`font_color`,";
					$dataValues .= "'".$font_color."',";
					
					break;
				case 'applied_page':
					$applied_page = str_replace("'", "''", $value);
					$dataKeys .= "`applied_page`,";
					$dataValues .= "'".$applied_page."',";
					
					break;
				case 'background_image_url':
					$background_image_url = str_replace("'", "''", $value);
					$dataKeys .= "`background_image_url`,";
					$dataValues .= "'".$background_image_url."',";
					
					break;
				case 'background_image_s3_path':
					$background_image_s3_path = str_replace("'", "''", $value);

					if(!empty($background_image_s3_path)){
						$rand = rand(0,999);
						$time = time();
						$extension = explode(".", $value);
						$extension = strtolower(end($extension));
						$hashvalue=hash('sha256',php_uname('n').$time.$rand);
						$s3path =  $channelname."/RecommendPackage/".$hashvalue.".".$extension;
						
						$filename = basename($value);
						$filepath=$ProductPhotoFilepath.$filename;
						if(file_exists($filepath))	
							$background_image_s3_path = uploadtoS3($filepath,$s3path);
						else
							$background_image_s3_path="N/A";	
						$dataKeys .= "`background_image_s3_path`,";
						$dataValues .= "'".$background_image_s3_path."',";

						$s3_check_flag = true;

					}

					break;
				case 'uproduct':
					$uproduct = $value;
					// foreach ($uproduct as $key => $val) {
					// 	echo $key.": ".$val['order']."->".$val['id']."<br>";
					// }
					break;
				case 'sproduct':
					$sproduct = $value;
					// foreach ($sproduct as $key => $val) {
					// 	echo $key.": ".$val['order']."->".$val['id']."<br>";
					// }
					break;

				
				default:				
					break;
			}
		}

		$dataKeys .= "`syschannelprofiles_id`,`activation_status`,`is_deleted`, `insert_time`, `update_time`";
		$dataValues .= "'".$syschannelprofiles_id."','1','0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP";

		$insertQ = "INSERT INTO `USRChannelProductRecommendationsPackages` (".$dataKeys.") VALUES (".$dataValues.");";

		if($s3_check_flag){
			// echo $insertQ;

			try{
				
				$stmtinsert = $dbLocal->prepare($insertQ);
				if($stmtinsert->execute()){
					$lastID = getLatestIdQ($dbLocal,"SELECT * FROM `USRChannelProductRecommendationsPackages` ORDER BY `id` DESC LIMIT 1");

					//2. Insert unrestricted data if available
					if(count($uproduct) > 0){
						addLoopUnrestrictedProd($uproduct,$dbLocal,$lastID,$syschannelprofiles_id,$activation_status);
					}

					if(count($sproduct) > 0){
						addLoopProd($sproduct,$dbLocal,$lastID,$syschannelprofiles_id,$activation_status);
					}

					echo "success";
					
				}else{
					echo "failed";
				}
					
			}catch(PDOException $e){
				echo "error in Line 143: -> ".$e->getMessage();
			}

		}else{
			echo "no s3 url path";
		}
	}else{
		echo "retrieve channel name error";
	}

		
	
	//json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);

		
	
	
}

function addLoopUnrestrictedProd($prodArray,$dbLocal,$lastID,$syschannelprofiles_id,$activation_status){
	try{
		foreach ($prodArray as $key => $val) {
			$insertQ2 = "INSERT INTO `USRChannelProductRecommendations`(`unrestricted_product_id`, `recommendation_packages_id`, `ranking`, `syschannelprofiles_id`, `activation_status`, `is_deleted`, `insert_time`) VALUES ('".$val['id']."','".$lastID."','".$val['order']."','".$syschannelprofiles_id."','1','0',CURRENT_TIMESTAMP)";
			$stmt = $dbLocal->prepare($insertQ2);
			if($stmt->execute()){
				
			}else{
				echo "error in line 157";
			}
		}	
	
	}catch(PDOException $e){
		echo "error in Line 171: -> ".$e.getMessage();
	}
}

function addLoopProd($prodArray,$dbLocal,$lastID,$syschannelprofiles_id,$activation_status){
	try{
		foreach ($prodArray as $key => $val) {
			$insertQ2 = "INSERT INTO `USRChannelProductRecommendations`(`product_id`, `recommendation_packages_id`, `ranking`, `syschannelprofiles_id`, `activation_status`, `is_deleted`, `insert_time`) VALUES ('".$val['id']."','".$lastID."','".$val['order']."','".$syschannelprofiles_id."','1','0',CURRENT_TIMESTAMP)";
			$stmt = $dbLocal->prepare($insertQ2);
			if($stmt->execute()){
				
			}else{
				echo "error line 174";
			}
		}

	
	}catch(PDOException $e){
		echo "error in Line 180: -> ".$e.getMessage();
	}
}
		

function getLatestIdQ($db,$query){
	try{
		$stmt = $db->prepare($query);
		if($stmt->execute()){
			$res = $stmt->fetchAll();
			return $res[0]['id'];
		}else{
			return "error";
		}
	}catch(PDOException $e){
		echo "error in Line 195: -> ".$e->getMessage();
		return "error";
	}
		
}

function addRecommendedRule($body){
	
	$packageid = "";
	$factor = "";
	$insert_time="";
	$update_time="";
	foreach($body as $key => $val){
		switch ($key) {
            case 'packages_id':
				$packageid = $val;
				      
              break;
            case 'factor_name':
              	$factor = $val;

              break;
            case 'insert_time':
              	$insert_time = $val;

              break;
            case 'update_time':
              	$update_time = $val;

              break;    
            default:
              # code...
              break;
         }
	}

	// insert fields to database
	try{
		$dbLocal = getdbhimirrorqa();
		$insertQ = "INSERT INTO  USRChannelProductRecommendationsPackagesMappingFactor (`packages_id`,`factor_name`,`update_time`,`is_deleted`,`insert_time`,`update_time`) VALUES ('".$packageid."','".$factor."',CURRENT_TIMESTAMP,'0','".$insert_time."','".$update_time."')";
		$stmtinsert = $dbLocal->prepare($insertQ);
		if($stmtinsert->execute()){
			echo "success";
		}
		else{
			echo "failed";
		}
	}
	catch(PDOException $e){
		echo "line 235 message: ".$e->getMessage();
	}
}

function editRecommendedProduct($channelid,$package_id,$jsonbody){
	global $ProductPhotoFilepath,$bucketname;
	$dbLocal = getdbhimirrorqa();
	$package_name = "";
	$syschannelprofiles_id = $channelid;
	$font_size = 0;
	$font_color = "";
	$applied_page = "";
	$insert_time = "";
	$update_time = "";
	$background_image_url = "";
	$background_image_s3_path = "";
	$activation_status = 0;	

	$unrestricted_order = 0;
	$unrestricted_id = "";
	$sproduct_order = 0;
	$sproduct_id = "";

	$is_deleted = 0;

	$uproduct = array();
	$sproduct = array();

	$setClause = "";

	$s3_check_flag = false;
	$channel_name_flag = false;
	$channelname = "";

	$selectChannelname = "SELECT * FROM `SYSChannelProfiles` WHERE `id` = '".$channelid."'";

	try{
		$stmtChannelName = $dbLocal->prepare($selectChannelname);
		if($stmtChannelName->execute()){
			$res = $stmtChannelName->fetchAll();
			if(count($res)>0){
				$channelname = $res[0]['name'];
				// echo json_encode($res);
				$channel_name_flag = true;
			}
			
		}
	}catch(PDOException $e){
		echo "error in Line  : -> ".$e->getMessage();
	}

	if($channel_name_flag){
		foreach ($jsonbody as $key => $value) {
			switch ($key) {
				case 'package_name':
					$package_name = str_replace("'", "''", $value);
					if($setClause == ""){
						$setClause = "`package_name` = '".$package_name."'";
					}else{
						$setClause .= ",`package_name` = '".$package_name."'";
					}

					break;
				case 'font_size':
					$font_size = $value;
					if($setClause == ""){
						$setClause = "`font_size` = '".$font_size."'";
					}else{
						$setClause .= ",`font_size` = '".$font_size."'";
					}
					
					break;
				case 'font_color':
					$font_color = str_replace("'", "''", $value);
					if($setClause == ""){
						$setClause = "`font_color` = '".$font_color."'";
					}else{
						$setClause .= ",`font_color` = '".$font_color."'";
					}
					
					break;
				case 'applied_page':
					$applied_page = str_replace("'", "''", $value);
					if($setClause == ""){
						$setClause = "`applied_page` = '".$applied_page."'";
					}else{
						$setClause .= ",`applied_page` = '".$applied_page."'";
					}
					
					break;
				case 'insert_time':
					$insert_time = $value;
					if($setClause == ""){
						$setClause = "`insert_time` = '".$insert_time."'";
					}else{
						$setClause .= ",`insert_time` = '".$insert_time."'";
					}
					
					break;
					
					break;
				case 'background_image_url':
					$background_image_url = str_replace("'", "''", $value);
					if($setClause == ""){
						$setClause = "`background_image_url` = '".$background_image_url."'";
					}else{
						$setClause .= ",`background_image_url` = '".$background_image_url."'";
					}
					
					break;
				case 'background_image_s3_path':
					$background_image_s3_path = str_replace("'", "''", $value);

					if(!empty($background_image_s3_path)){
						if(strpos($background_image_s3_path,"http") !== false){
							if(strpos($background_image_s3_path,$bucketname) !== false){

							}else{
								$rand = rand(0,999);
								$time = time();
								$extension = explode(".", $value);
								$extension = strtolower(end($extension));
								$hashvalue=hash('sha256',php_uname('n').$time.$rand);
								$s3path =  $channelname."/RecommendPackage/".$hashvalue.".".$extension;	
								$filename = basename($value);
								$filepath=$ProductPhotoFilepath.$filename;
								if(file_exists($filepath))	
									$background_image_s3_path = uploadtoS3($filepath,$s3path);
								else
									$background_image_s3_path="N/A";

								if($setClause == ""){
									$setClause = "`background_image_s3_path` = '".$background_image_s3_path."'";
								}else{
									$setClause .= ",`background_image_s3_path` = '".$background_image_s3_path."'";
								}
							}
						}else{
							$rand = rand(0,999);
							$time = time();
							$extension = explode(".", $value);
							$extension = strtolower(end($extension));
							$hashvalue=hash('sha256',php_uname('n').$time.$rand);
							$s3path =  $channelname."/RecommendPackage/".$hashvalue.".".$extension;	
							$filename = basename($value);
							$filepath=$ProductPhotoFilepath.$filename;
							if(file_exists($filepath))	
								$background_image_s3_path = uploadtoS3($filepath,$s3path);
							else
								$background_image_s3_path="N/A";

							if($setClause == ""){
								$setClause = "`background_image_s3_path` = '".$background_image_s3_path."'";
							}else{
								$setClause .= ",`background_image_s3_path` = '".$background_image_s3_path."'";
							}
						}
							
					}

					break;
				case 'activation_status':
					$activation_status = $value;
					if($setClause == ""){
						$setClause = "`activation_status` = '".$activation_status."'";
					}else{
						$setClause .= ",`activation_status` = '".$activation_status."'";
					}
					
					break;
				case 'uproduct':
					$uproduct = $value;
					// foreach ($uproduct as $key => $val) {
					// 	echo $key.": ".$val['order']."->".$val['id']."<br>";
					// }
					break;
				case 'sproduct':
					$sproduct = $value;
					// foreach ($sproduct as $key => $val) {
					// 	echo $key.": ".$val['order']."->".$val['id']."<br>";
					// }
					break;

				
				default:				
					break;
			}
		}

		$setClause .= ",`update_time` = CURRENT_TIMESTAMP";

		//json_encode($val, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
		try{
			$updateQ = "UPDATE `USRChannelProductRecommendationsPackages` SET $setClause WHERE `USRChannelProductRecommendationsPackages`.`id` = '".$package_id."';";
			// echo $updateQ;
			
			$stmtupdate = $dbLocal->prepare($updateQ);
			if($stmtupdate->execute()){
				//update recommended prod list
				
				updateALL($package_id,$dbLocal);
				if(count($uproduct) > 0){
					updateLoopUnrestrictedProd($uproduct,$dbLocal,$package_id,$syschannelprofiles_id);
				}

				if(count($sproduct) > 0){
					updateLoopProd($sproduct,$dbLocal,$package_id,$syschannelprofiles_id);
				}

				echo "success";

			}else{
				echo "update failed<br>";
			}

				
		}catch(PDOException $e){
			echo "error in Line 500: -> ".$e->getMessage();
		}

	}else{
		echo "retrieve channelname error";
	}

		
		
}

function updateLoopUnrestrictedProd($uproduct,$dbLocal,$package_id,$syschannelprofiles_id){
	try{
		foreach ($uproduct as $key => $val) {
			$insertQ2 = "INSERT INTO `USRChannelProductRecommendations`(`unrestricted_product_id`, `recommendation_packages_id`, `ranking`, `syschannelprofiles_id`, `activation_status`, `is_deleted`, `insert_time`) VALUES ('".$val['id']."','".$package_id."','".$val['order']."','".$syschannelprofiles_id."','1','0',CURRENT_TIMESTAMP)";
			$stmt = $dbLocal->prepare($insertQ2);
			if($stmt->execute()){
			
			}else{
				echo "error in line 435 <br>";
			}
		}	
	}catch(PDOException $e){
		echo "error in Line 431: -> ".$e->getMessage();
	}
		

}

function updateLoopProd($sproduct,$dbLocal,$package_id,$syschannelprofiles_id){
	try{
		foreach ($sproduct as $key => $val) {
			$insertQ2 = "INSERT INTO `USRChannelProductRecommendations`(`product_id`, `recommendation_packages_id`, `ranking`, `syschannelprofiles_id`, `activation_status`, `is_deleted`, `insert_time`) VALUES ('".$val['id']."','".$package_id."','".$val['order']."','".$syschannelprofiles_id."','1','0',CURRENT_TIMESTAMP)";
			$stmt = $dbLocal->prepare($insertQ2);
			if($stmt->execute()){
				
			}else{
				echo "error line 456<br>";
			}
		}
	}catch(PDOException $e){
		echo "error in Line 449: -> ".$e->getMessage();
	}
}

function updateALL($package_id,$dbLocal){
	try{
		$query1 = "UPDATE `USRChannelProductRecommendations` SET `is_deleted` = '1' WHERE `recommendation_packages_id` = '".$package_id."'";

		$stmt1 = $dbLocal->prepare($query1);
		if($stmt1->execute()){
			
		}else{
			echo "update2 failed<br>";
		}
	}catch(PDOException $e){
		echo "error in Line 464: -> ".$e->getMessage();
	}

}

function deleteRecommendedProduct($package_id){

	$dbLocal = getdbhimirrorqa();
	
	try{
		$deleteQ = "UPDATE USRChannelProductRecommendationsPackages SET `is_deleted` = 1 WHERE `id` = '".$package_id."'";
		$stmtdelete = $dbLocal->prepare($deleteQ);
		if($stmtdelete->execute()){
			echo "success";
		}else
			echo "failed";

	}
	catch(PDOException $e){
		echo "line 483 message: ".$e->getMessage();
	}
}

function deleteRecommendedRule($rule_id){

	$dbLocal = getdbhimirrorqa();

	try{
		$stmtget = $dbLocal->prepare("SELECT * FROM B2BRecommendedRule WHERE recommended_rule_id = '".$rule_id."'");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		$countDup = count($result);
		
		if(!empty($countDup)){
			$deleteQ = "DELETE from B2BRecommendedRule WHERE recommended_rule_id = $rule_id";
			$stmtdelete = $dbLocal->prepare($deleteQ);
				if($stmtdelete->execute()){
					echo json_encode(array("response"=>"Successfully Deleted $rule_id"));
				}
				else{
					echo "failed";
				}
		}else{
			echo "$rule_id does not exist in the database";
		}

	}
	catch(PDOException $e){
		echo "line 512 message: ".$e->getMessage();
	}
}

function editProductOrder($package_id, $jsonbody){

	$dbLocal = getdbhimirrorqa();

	//1. Check if id exist
	$stmtget = $dbLocal->prepare("SELECT * FROM B2BRecommendedProducts WHERE package_id = '".$package_id."'");
	$stmtget->execute();	
	$result= $stmtget->fetchAll();
	$countDup = count($result);
	
	//2. split json body 
	if(!empty($countDup)){
		//2; Update new Product list
		try{
			$updateQ = "UPDATE B2BRecommendedProducts SET `package_list` ='".json_encode($jsonbody)."' WHERE package_id = $package_id";
			//echo $updateQ;
			$stmtupdate = $dbLocal->prepare($updateQ);
				if($stmtupdate->execute()){
					echo json_encode(array("response"=>"Successfully Updated productList Order for $package_id"));
				}
				else{
					echo "failed order update";
				}
		}
		catch(PDOException $e){
			echo "line 541 message: ".$e->getMessage();
		}
	}
	else{
		echo "$package_id does not exist";
	}

}

function getRecommendedRuleList(){

	$dbLocal = getdbhimirrorqa();

	try{
		$selectQ = "SELECT * FROM USRChannelProductRecommendationsPackagesMappingFactor WHERE is_deleted = '0'";
		// echo $selectQ;
		$stmtselect = $dbLocal->prepare($selectQ);
		if($stmtselect->execute()){
			$res = $stmtselect->fetchAll();
			echo json_encode($res);
		}else{
			echo "failed";
		}

	}catch(PDOException $e){
		echo "line 647 Message: ".$e->getMessage();
	}
		
}

function getRecommendedRulePackageList($recommend_rule_id, $rule_name, $block_name){

	try{
		$dbLocal = getdbhimirrorqa();
		$selectQ = "SELECT recommended_package_list from B2BRecommendedRule WHERE recommended_rule_id = '$recommend_rule_id' AND mapping_rule_name LIKE '%$rule_name%' AND block LIKE '%$block_name%' ";
		// echo $selectQ;
		$stmtselect = $dbLocal->query($selectQ);
		$result= $stmtselect->fetchAll();
		if($result !='' && !empty($result)){
			echo json_encode($result);
		}else{
			echo "No records found for recommended_id = $recommend_rule_id , mapping_rule_name = $rule_name and block = $block_name... ";		
		}
	}catch(PDOException $e){
		echo "line 665 ERROR Message: ".$e->getMessage();
	}
}

function editRecommendedRuleOrder($recommend_rule_id,$rule_name,$block_name,$jsonbody){


	$dbLocal = getdbhimirrorqa();
	$updpackagelist = json_encode($jsonbody);

	try{
		//1. Check if id is existing
		$selectQ = "SELECT * FROM B2BRecommendedRule WHERE recommended_rule_id = '$recommend_rule_id' AND mapping_rule_name = '$rule_name' AND block = '$block_name'" ;
		$stmtget =  $dbLocal->prepare($selectQ);
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		$countDup = count($result);

		if(!empty($countDup)){
			//2. update
			$updateQ = "UPDATE B2BRecommendedRule SET recommended_package_list = '$updpackagelist' WHERE recommended_rule_id = $recommend_rule_id AND mapping_rule_name = '$rule_name' AND block = '$block_name'";
			$stmtupdate =  $dbLocal->prepare($updateQ);
			if($stmtupdate->execute()){
				echo "Successfully updated";
			}else{
				echo "failed update";
			}
		$stmtget->execute();
		}else{
			echo "Entry $rule_name with recommend_rule_id = $recommend_rule_id does not exist";
		}
	}catch(PDOException $e){
		echo "line 697 Message: ".$e->getMessage();
	}
}

?>