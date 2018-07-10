<?php

include("globalLocal.php");

function getApplicationList($channelid){

	try{
		$dbLocal = getdbhimirrorqa();
		$getApplicationQuery = "SELECT platform_name, app_name, app_id, app_key, tracker_id, action, status, createtime from SYSChannelApplications  WHERE syschannelprofiles_id LIKE '%$channelid' AND status !=0 ";
		$stmt = $dbLocal->query($getApplicationQuery);
		$stmt->execute();
		$result= $stmt->fetchAll();
		echo json_encode($result);
	}catch(PDOException $e){
		echo "failed 15\n".$getApplicationQuery;
	}
}

function createApplication($jsonbody,$channelid){

	global $dbnamelocal2;
	$dbLocal = getdbhimirrorqa();

	try{
		$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal2."' AND TABLE_NAME = 'SYSChannelApplications'");
		$stmtgetProduct->execute();	
		$result= $stmtgetProduct->fetchAll();	
		$keylist="";
		$valuelist="";
		$pnameArr = "";
		$appnameArr= "";
		$appidArr = "";
		$appkeyArr = "";
		$trackArr = "";
		
		foreach ($jsonbody as $key=> $value){
			foreach($value as $key2=> $value2){
				if($key2 == 'platform_name')  $pnameArr = checkEmpty($value2);
				elseif($key2 == 'app_name')  $appnameArr = checkEmpty($value2);
				elseif($key2 == 'app_id')  $appidArr = checkEmpty($value2);
				elseif($key2 == 'app_key') $appkeyArr = checkEmpty($value2);
				elseif($key2 == 'tracker_id') $trackArr = checkEmpty($value2);
			}
			$insertApplication="insert into SYSChannelApplications (syschannelprofiles_id,platform_name,app_name,app_id,app_key,tracker_id) VALUES ('$channelid','$pnameArr', '$appnameArr','$appidArr','$appkeyArr','$trackArr')";	
			$stmtInsert = $dbLocal->prepare($insertApplication);
			$stmtInsert->execute();
		}	
	}catch(PDOException $e){
		echo $e->getMessage()."line49 $insertApplication\n";
	}
}

function updateApplicationStatus($app_id,$action){

	try{
		$dbLocal = getdbhimirrorqa();
		$updateQ = "UPDATE SYSChannelApplications SET action = $action WHERE id LIKE '%$app_id%'";
		$stmtUpdate = $dbLocal->prepare($updateQ);
		$stmtUpdate->execute();
		echo "success";
	}catch(PDOException $e){
		echo $e->getMessage()."line 63 $updateQ\n";
	}
}

function deleteApplication($app_id,$action){

	try{
		$dbLocal = getdbhimirrorqa();
		$deleteQ = "UPDATE SYSChannelApplications SET status = $action WHERE id LIKE '%$app_id%'";
		$stmtUpdate = $dbLocal->prepare($deleteQ);
		$stmtUpdate->execute();
		echo "success";
	}catch(PDOException $e){
		echo $e->getMessage()."line 76 $updateQ\n";
	}
}
?>