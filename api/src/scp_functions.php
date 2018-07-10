<?php

//Get database connection
function listAllAge($ageList){
		
	$tempList = '';
	for($counter = 0; $counter < count($ageList); $counter++){
		list($start, $end) = array_pad(explode('-', $ageList[$counter],2),2,null);
		
		for($tempCount = $start; $tempCount <= $end; $tempCount++){
			if($tempCount == $end)
				$tempList .= $tempCount;
			else
				$tempList .= $tempCount.",";
		}

		if($counter < count($ageList)-1)
			$tempList .= ",";
	}

	return $tempList;
}
function listAll($countryList){
	$tempRet = '';
	for($counter=0; $counter < count($countryList); $counter++){
		if($counter == count($countryList)-1)
			$tempRet .= "'".str_replace("'", "''",$countryList[$counter])."'";
		else
			$tempRet .= "'".str_replace("'", "''",$countryList[$counter])."',";
	}
	return $tempRet;
}

function listAllOrder($orderList){

	$ordernum = array();
	for ($counter = 1; $counter <= $orderList ; $counter++) { 
		// echo $counter."\n";
		array_push($ordernum, $counter);
	}
	return $ordernum;
}

//global function
function channelChecker($channelid){

	$dbLocal = getdbhimirrorqa();
	
	try{
		$query = "SELECT * FROM `SYSChannelProfiles` WHERE `id` = '$channelid'";
		$stmt = $dbLocal->prepare($query);

		if($stmt->execute()){
			$res = $stmt->fetchAll();

			if(count($res)>0){
				return true;
			}else{
				echo '{"response":"no result","errcode":"b2ber004"}';
				return false;
			}

		}else{
			echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
			return false;
		}

	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-69"}'; 
		return false;
	}

}

//global function
function adminChecker($id){

	$dbLocal = getdbhimirrorqa();
	
	try{
		$query = "SELECT * FROM `SYSChannelUsers` WHERE `id` = '$id' AND  `is_admin` = 1";
		$stmt = $dbLocal->prepare($query);

		if($stmt->execute()){
			$res = $stmt->fetchAll();

			if(count($res)>0){
				return true;
			}else{
				echo '{"response":"Invalid Admin ID","errcode":"b2ber010"}';
				return false;
			}

		}else{
			echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
			return false;
		}

	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-100"}'; 
		return false;
	}

}

?>