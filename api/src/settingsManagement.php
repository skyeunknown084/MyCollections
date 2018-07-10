<?php

include("globalLocal.php");

function getUserSettings($channelid){


	$dbLocal = getdbhimirrorqa();
	$settingsArrayFinal = array();
	$settingNameArray = array();
	$valuesArray = array();
	$statusArray = array();

	$getProfileQuery = "SELECT SYSChannelBrand.name as brand_name, SYSChannelProfiles.name as group_name, SYSChannelProfiles.syschannelbrand_id as brand_id, SYSChannelProfiles.title as group_title, SYSChannelProfiles.logo_url as group_logo, SYSChannelProfiles.status as group_status, SYSChannelProfiles.note as group_note FROM SYSChannelProfiles INNER JOIN SYSChannelBrand ON SYSChannelProfiles.syschannelbrand_id = SYSChannelBrand.id WHERE SYSChannelProfiles.id LIKE '%$channelid' ";
	$stmt = $dbLocal->query($getProfileQuery);
	$stmt->execute();
	$profile_result= $stmt->fetchAll();

	foreach($profile_result as $value){
		foreach($value as $innerKey=> $innerValue){
			$settingsArrayFinal[$innerKey] = $innerValue;
		}
	}

	$getSettingQuery = "Select SYSChannelFunctions.*,MYfunction.grand_parent_name2,MYfunction.grand_parent_name,parent_name,name from SYSChannelFunctions INNER JOIN (Select parent.id,grand_parent_name2,grand_parent_name,parent_name,parent.name,status from (Select parent.parent_id,parent_name,grand_parent,name,id,status from SYSAllFunctions Left JOIN (Select id as parent_id,parent_id as grand_parent,name as parent_name from SYSAllFunctions )as parent on parent.parent_id=SYSAllFunctions.parent_id) as parent LEFT JOIN (Select id,name as grand_parent_name from SYSAllFunctions) as grandparent on grandparent.id=parent.grand_parent LEFT JOIN (Select id as grand_parent2 ,parent_id as grand_parent_name2 FROM SYSAllFunctions) as grandparent2 on grandparent2.grand_parent2 = grandparent.id)as MYfunction on MYfunction.id=SYSChannelFunctions.sysallfunctions_id where SYSChannelFunctions.syschannelprofiles_id LIKE '%$channelid%'";

	$stmt2 = $dbLocal->query($getSettingQuery);
	$stmt2->execute();
	$result= $stmt2->fetchAll();
	$strip = array("[", "]", '"');

	for($i=0; $i<count($result);$i++){
		foreach($result[$i] as $key=>$value){
			if($key=='name'){
				$settingNameArray[ltrim(getMain($result[$i]['grand_parent_name2'])."_".$result[$i]['grand_parent_name']."_".$result[$i]['parent_name']."_".$value, "_")] = str_replace($strip, "", explode(",", $result[$i]['value']));
			}                                                                                                                                                                                     
		}
	}

	$i= 0;
	foreach($settingNameArray as $key=>$value){
		if(is_array($value)){
			if(!count($value)==0){
				foreach($value as $key2 => $value2){
					$settingsArrayFinal[$key] = array("status"=> $result[$i]['status'], "value"=>checkEmptyD($value), "order"=> $result[$i]['layout_order'], "date"=>$result[$i]['createtime']);
				}	 
			}else{
				$settingsArrayFinal[$key] = array("status" => $result[$i]['status'], "value"=>"N/A", "order"=> $result[$i]['layout_order'], "date"=>$result[$i]['createtime']);
			}
		}
		$i++;
	}

	echo json_encode($settingsArrayFinal);
}

function createSettings($jsonbody){

	global $ProductPhotoFilepath;
	$dbLocal = getdbhimirrorqa();
	$profileArray = array('group_name','brand_id','group_title','group_logo','group_status','group_note','createtime');
	$distinctIArray = array();
	$responseArray = array();
	$successFlag = false;
	$successFlag2 = false;
	$keyListProfile = "";
	$valueListProfile = "";
	$lastIDChannel ="";
	$photo = "";
	$s3path = "";
	$channel_name = "";

	$getDistinctIDQ = "SELECT DISTINCT id FROM `SYSAllFunctions` ";
	$stmtgetIds = $dbLocal->prepare($getDistinctIDQ); 
	$stmtgetIds->execute();
	$getidList= $stmtgetIds->fetchAll();
	
	for($i=0; $i<count($getidList); $i++){
		foreach($getidList[$i] as $key=>$value){
			$distinctIArray[$i] = $value;
		}
	}

	$responseArray['brand_name'] = $jsonbody['brand_name'];

	foreach($jsonbody as $key => $value){
		if(in_array($key, $profileArray)){
			$responseArray[$key] = $value;
			if(empty($valueListProfile)){
				$valueListProfile.= "'".checkEmpty($value)."'";
			}else{
				if($key != 'createtime' && $key != 'group_logo'){
					$valueListProfile.= ",'".checkEmpty($value)."'";
				}elseif($key == 'group_logo'){
					#skip
				}else{
					$valueListProfile.= ",".$value;
				}
			}
		}
	}

	try{
		$insertProfileQ = "INSERT INTO SYSChannelProfiles (name,syschannelbrand_id,title,status,note,createtime) VALUES ($valueListProfile)";
		$stmtInsertProfile = $dbLocal->prepare($insertProfileQ);
		$stmtInsertProfile->execute();
		$stmtLastInsertID = $dbLocal->prepare("SELECT id from SYSChannelProfiles ORDER by createtime DESC LIMIT 1");
		$stmtLastInsertID->execute();
		$getLastIDresult = $stmtLastInsertID->fetchAll();
		$lastIDChannel = $getLastIDresult[0]['id'];
		$successFlag = true;
	}catch(PDOException	$e){
		echo "Failed 129".$insertProfileQ;
	}

	$selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id LIKE '%".$lastIDChannel."%'";
	$stmtgetChannelName = $dbLocal->prepare($selectchannelname);
	try{
		$stmtgetChannelName->execute();
		$res = $stmtgetChannelName->fetchAll();
		foreach ($res as $key => $value) {
		    $channel_name = $value['name'];
		    $successFlag =true;
		}
	}catch (PDOException $e) {
	 	echo " failed 81".$e;
	}

	if($successFlag){
		foreach($jsonbody as $key => $value){
			if($key == 'group_logo'){
				$rand = rand(0,999);
				$time = time();
				$extension = explode(".", $value);
				$extension = strtolower(end($extension));
				$hashvalue=hash('sha256',php_uname('n').$time.$rand);
				$hashvalue=hash('sha256',php_uname('n').$time.$rand);
				$filename = basename($jsonbody['group_logo']);
				$filepath = $ProductPhotoFilepath.$filename;
				if(file_exists($filepath)){			
					$s3path =  "$channel_name/Profile/".$hashvalue.".".$extension;																		
					$photo = uploadtos3($filepath, $s3path);							 	 	
				}else{											
					$photo = "N/A";
					$s3path = "N/A";
				}
			}
		}

		try{
			$updatePhotoQ = "UPDATE SYSChannelProfiles SET logo_url = '$photo', logo_s3path = '$s3path' WHERE id LIKE '%".$lastIDChannel."%'";
			$stmtUpdtProfile = $dbLocal->prepare($updatePhotoQ);
			$stmtUpdtProfile->execute();
			$successFlag2 = true;
		}catch(PDOException $e){
			echo "failed 170".$updatePhotoQ;
		}
	}

	$i=0;
	if($successFlag2){
		foreach($jsonbody as $key => $value){
			if(!in_array($key, $profileArray)){
				if($key!='brand_name'){
					foreach($value as $key2 => $value2){
						//$settingID = getSettingId($key);
						//if(in_array($settingID,$distinctIArray)){
						if(is_array($value2)){
							try{
								$responseArray[$key] = array("status"=> $value['status'], "value"=> json_encode(checkArray($value2),JSON_UNESCAPED_SLASHES), "order"=>checkEmptyInt($value['order']));
								$insertSettingsQ = "INSERT INTO SYSChannelFunctions (syschannelprofiles_id, sysallfunctions_id, value, status, layout_order, createtime) VALUES ('$lastIDChannel', '$distinctIArray[$i]','".json_encode(checkArray($value2), JSON_UNESCAPED_SLASHES)."','".$value['status']."',".checkEmptyInt($value['order']).",  CURRENT_TIMESTAMP)\n";
								$stmtinsertSettings = $dbLocal->prepare($insertSettingsQ);
								$stmtinsertSettings->execute();
								//echo "Success inserting settings for $key = $distinctIArray[$i] \n";
							}catch(PDOException $e){
								echo "Failed 105";
							}
						}
					}$i++;
				}
			}
		}
   		echo json_encode($responseArray);
	}else{
		echo "167 failed inserting settings";
	}
}

function editSettings($jsonbody, $channelid){

	global $ProductPhotoFilepath;
	$dbLocal = getdbhimirrorqa();
	$profileArray = array('group_name','brand_id','group_title','group_logo','group_status','group_note','createtime');
	$distinctIArray = array();
	$responseArray = array();
	$group_name = "";
	$brand_id = "";
	$group_title = "";
	$group_logo = "";
	$group_status = "";
	$group_note = "";
	$successFlag = false;
	$keyListProfile = "";
	$valueListProfile = "";
	$photo = "";
	$s3path = "";
	$channel_name = "";

	$getDistinctIDQ = "SELECT DISTINCT id FROM `SYSAllFunctions` ";
	$stmtgetIds = $dbLocal->prepare($getDistinctIDQ); 
	$stmtgetIds->execute();
	$getidList= $stmtgetIds->fetchAll();
	
	for($i=0; $i<count($getidList); $i++){
		foreach($getidList[$i] as $key=>$value){
			$distinctIArray[$i] = $value;
		}
	}

	$selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id LIKE '%".$channelid."%'";
	$stmtgetChannelName = $dbLocal->prepare($selectchannelname);
	try{
		$stmtgetChannelName->execute();
		$res = $stmtgetChannelName->fetchAll();
		foreach ($res as $key => $value) {
		    $channel_name = $value['name']; 
		    $successFlag =true;
		}
	}catch (PDOException $e) {
	 	echo " failed 81".$e;
	}

	$responseArray['brand_name'] = $jsonbody['brand_name'];

	foreach($jsonbody as $key => $value){
		if(in_array($key, $profileArray)){
			if($key == 'group_name'){
				$group_name = $value;
				$responseArray[$key] = $value;
			}
			elseif($key == 'brand_id'){
				$brand_id = $value;
				$responseArray[$key] = $value;
			} 
			elseif($key == 'group_title'){
				$group_title = $value;
				$responseArray[$key] = $value;
			} 
			elseif($key == 'group_logo'){
				$group_logo = $value;
				$responseArray[$key] = $value;
			} 
			elseif($key == 'group_status'){
				$group_status = $value;
				$responseArray[$key] = $value;
			} 
			elseif($key == 'group_note'){
				$group_note = $value;
				$responseArray[$key] = $value;	
			}
		}
	}

	foreach($jsonbody as $key => $value){
		if($key == 'group_logo'){
			$rand = rand(0,999);
			$time = time();
			$extension = explode(".", $value);
			$extension = strtolower(end($extension));
			$hashvalue=hash('sha256',php_uname('n').$time.$rand);
			$hashvalue=hash('sha256',php_uname('n').$time.$rand);
			$filename = basename($jsonbody['group_logo']);
			$filepath = $ProductPhotoFilepath.$filename;
			if(file_exists($filepath)){			
				$s3path =  "$channel_name/Profile/".$hashvalue.".".$extension;																		
				$photo = uploadtos3($filepath, $s3path);
				$responseArray[$key] = $photo;							 	 	
			}else{											
				$photo = "N/A";
				$s3path = "N/A";
				$responseArray[$key] = $photo;
			}
		}
	}

	try{
		$updateProfileQ = "UPDATE SYSChannelProfiles SET name = '$group_name' , syschannelbrand_id = '$brand_id', title = '$group_title' ,status = '$group_status',note = '$group_note', logo_url = '$photo', logo_s3path = '$s3path' WHERE id LIKE '%$channelid%'";
		$stmtUpdateProfile = $dbLocal->prepare($updateProfileQ);
		$stmtUpdateProfile->execute();
		$successFlag = true;
	}catch(PDOException	$e){
		echo "Failed 286".$updateProfileQ;
	}

	if($successFlag){
		foreach($jsonbody as $key => $value){
			if(!in_array($key, $profileArray)){
				if($key!='brand_name'){
					foreach($value as $key2 => $value2){
						$settingID = getSettingId($key);
						if(in_array($settingID,$distinctIArray)){
							if(is_array($value2)){
								try{
									$responseArray[$key] = array("status"=> $value['status'], "value"=> json_encode(checkArray($value2),JSON_UNESCAPED_SLASHES), "order"=>checkEmptyInt($value['order']));
									$UpdateSettingsQ = "UPDATE SYSChannelFunctions SET value = '".json_encode(checkArray($value2,JSON_UNESCAPED_SLASHES))."', status = '".$value['status']."', layout_order = ".checkEmptyInt($value['order'])." WHERE sysallfunctions_id LIKE '%$settingID%'";
									$stmtUpdateSettings = $dbLocal->prepare($UpdateSettingsQ);
									$stmtUpdateSettings->execute();
								}catch(PDOException $e){
									echo "Failed 304 --- $UpdateSettingsQ";
								}
							}
						}  
					}
				}
			}
		}
   		echo json_encode($responseArray);
	}else{
		echo "312 failed updating settings";
	}
}

function getSettingId($name){

	$dbLocal = getdbhimirrorqa();
	$getQuery = "SELECT id from SYSAllFunctions WHERE name LIKE '%$name%'";
	$stmt = $dbLocal->query($getQuery);
	$stmt->execute();
	$result= $stmt->fetchAll();

	if(empty($result)){
		return false;
	}else{
		return $result[0]['id'];	
	}
}

function checkArray($value){

	if(is_array($value)){
		return $value;
	}else{
		return "N/A";
	}
}

function checkEmptyD($value){

	foreach($value as $value2){
		if($value2 !=''){
			return $value;
		}else{
			return ["N/A"];
		}	
	}
	
}

function getMain($value){
	
	$dbLocal = getdbhimirrorqa();

	try{
		if($value == "" || $value == NULL){
			return "";
		}else{
			$select1 = "SELECT * FROM `SYSAllFunctions` WHERE id LIKE '%$value%' ";
			$stmt = $dbLocal->query($select1);
			$stmt->execute();
			$result= $stmt->fetchAll();
			$parent_id_2 = $result[0]['name'];
					
			return $parent_id_2;
		}
	}catch(PDOException $e){
		echo "failed 382".$e;
	}
}

?>