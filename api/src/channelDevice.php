<?php

function channelDeviceList($channelid){
	$selectQuery = "SELECT * FROM SYSChannelDevices WHERE syschannelprofiles_id = '$channelid'";
	try{
		$dbLocal = getdbhimirrorqa();
		$stmtget = $dbLocal->prepare($selectQuery);
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		echo json_encode($result);
	}catch(PDOException $e){
		echo "ERROR";
	}
}

function channelDeviceFilterList($body){
	$selectQuery = "SELECT SYSChannelDevices.*, SYSChannelProfiles.name FROM SYSChannelDevices INNER JOIN SYSChannelProfiles ON SYSChannelProfiles.id=SYSChannelDevices.syschannelprofiles_id";

	$whereClause = '';
	if (!empty($body)) {
		foreach ($body as $key => $value) {
			switch ($key) {
				case 'model':						
					$tempModel = listAll($value);
					if (empty($whereClause)) {
						$whereClause .= " model IN (".$tempModel.") ";
					}else{
						$whereClause .= "AND model IN (".$tempModel.") ";
					}
					break;
				case 'name':
					$tempName = listAll($value);
					if (empty($whereClause)) {
						$whereClause .= " name IN (".$tempName.") ";
					}else{
						$whereClause .= "AND name IN (".$tempName.") ";
					}
					break;
				case 'serial_number':
					$tempSN = listAll($value);	
					if (empty($whereClause)) {
						$whereClause .= " serial_number IN (".$tempSN.") ";		
					}else{
						$whereClause .= "AND serial_number IN (".$tempSN.") ";
					}
					break;
				case 'mac_address':
					$tempMAC = listAll($value);	
					if (empty($whereClause)) {
						$whereClause .= " mac_address IN (".$tempMAC.") ";
					}else{
						$whereClause .= "AND mac_address IN (".$tempMAC.") ";
					}
					break;
				case 'status':
					$tempStatus = listAll($value);
					if (empty($whereClause)) {
						$whereClause .= " SYSChannelDevices.status IN (".$tempStatus.") ";
					}else{
						$whereClause .= "AND SYSChannelDevices.status IN (".str_replace("'", "", $tempStatus).") ";
					}
					break;
				default:
					# code...
					break;
			}
		}
	}
		

		if (!empty($whereClause)) {
			$selectQuery .=" WHERE ". $whereClause;
		}
	
	
	$dbLocal 	= getdbhimirrorqa();

	try{
		$stmt = $dbLocal->prepare($selectQuery);
		if($stmt->execute()){
			$res = $stmt->fetchAll();
			$numofdata = $stmt->rowCount();
			if($numofdata > 0){
				echo json_encode($res);
			}else
				echo '{"response":"no result","errcode":"b2ber004"}';
		}else
			echo '{"response":"SMT Execute Error","errcode":"b2ber005"}';
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-87"}';
	}
}


?>