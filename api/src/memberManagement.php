<?php

function memberManagementList($channelid){
	$selectQuery = "SELECT *, YEAR(CURDATE()) - 
YEAR(birthday) - 
IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birthday), '-', DAY(birthday)) ,'%Y-%c-%e') > CURDATE(), 1, 0) 
AS age FROM SYSChannelMembers WHERE syschannelprofiles_id = '$channelid' AND status = '1'";
	try{
		$dbLocal = getdbhimirrorqa();
		$stmtget = $dbLocal->prepare($selectQuery);
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		echo json_encode($result);
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-15"}';
	}
}

function memberManagementTotal($channelid){
	$selectQuery="SELECT COUNT(*) as TotalCount FROM `SYSChannelMembers` WHERE syschannelprofiles_id = '$channelid' AND status = '1'";
	

	$dbLocal 	= getdbhimirrorqa();
	try{
		$stmt = $dbLocal->prepare($selectQuery);
		if($stmt->execute()){
			$result = $stmt->fetchAll();

			foreach ($result as $rowValue) {
				$countdata = $rowValue['TotalCount'];
			}
			
		}else{
			$countdata = '0';
		}
		echo $countdata;
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-38"}';
	}
}

function memberManagementFilterSearch($body,$channelid){
	$selectQuery = "select * from (SELECT *,( YEAR(CURDATE()) - 
YEAR(birthday) - 
IF(STR_TO_DATE(CONCAT(YEAR(CURDATE()), '-', MONTH(birthday), '-', DAY(birthday)) ,'%Y-%c-%e') > CURDATE(), 1, 0) 
*1) AS age FROM SYSChannelMembers) as member WHERE syschannelprofiles_id = '$channelid' AND status = '1' ";

	
	
	$whereClause = '';
		foreach ($body as $key => $value) {
			switch ($key) {
				case 'Screatetime':
					$whereClause .= "AND createtime >='$value'";
					break;
				case 'Ecreatetime':
					$whereClause .= "AND createtime <= '$value'";
					break;
				case 'account':		
					$whereClause .= "AND account LIKE '%$value%'";
					break;
				case 'id_card':		
					$whereClause .= "AND id_card LIKE '%$value%'";
					break;
				case 'phone_number':
					$whereClause .= "AND phone_number LIKE '%$value%'";
					break;
				case 'gender':
					$tempRangeGen = listAll($value);
					$whereClause .= "AND gender IN (".$tempRangeGen.")";
					break;
				case 'age':						
					$tempRange = listAllAge($value);
					$whereClause .= " AND Age IN (".$tempRange.") ";
					break;
				default:
					# code...
					break;
			}
		}
		
	$selectQuery .=$whereClause;
	
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
		echo '{"response":"PDO Exception","errcode":"b2ber003-96"}';
	}
}

function createnewMember($jsonbody,$channelid){
	global $dbnamelocal; 
	$dbLocal = getdbhimirrorqa();
	$channel_name = "";
	$selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id = '$channelid'";
	$stmt = $dbLocal->prepare($selectchannelname);
	try {
		$stmt->execute();
		$res = $stmt->fetchAll();
		foreach ($res as $key => $value) {
			$channel_name = $value['name'];
		}
		
	} catch (PDOException $e) {
		echo '{"response":"PDO Exception","errcode":"b2ber003-114"}';
	}
	try {
		$stmtgetMember = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'SYSChannelMembers'");
			$stmtgetMember->execute();	
			$result= $stmtgetMember->fetchAll();	
			$keylist="";
			$valuelist="";

			if (checkMemberIfExist($jsonbody,$channelid)) {
				echo '{"response":"Member Exist","errcode":"b2ber008"}';
			}else{
				foreach ($jsonbody as $key => $value){
				$val=getInsertQueryMember($key,$value,$result);
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

			

			$insertMember="insert into SYSChannelMembers ($keylist, `syschannelprofiles_id`,updatetime) VALUES ($valuelist, '$channelid',CURRENT_TIMESTAMP)";
			$stmtInsert = $dbLocal->prepare($insertMember);
			$stmtInsert->execute();
			echo "success";
			}

			

	}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-150"}';
				$checkFlag = false;
			}
	

}


function editMember($memID, $jsonbody,$channelid){
	global $dbnamelocal; 
	$dbLocal = getdbhimirrorqa();
	$channel_name = "";
	$selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id = '$channelid'";
	$stmt = $dbLocal->prepare($selectchannelname);
	try {
		$stmt->execute();
		$res = $stmt->fetchAll();
		foreach ($res as $key => $value) {
			$channel_name = $value['name'];
		}
		
	} catch (PDOException $e) {
		echo '{"response":"PDO Exception","errcode":"b2ber003-172"}';
	}
	try{
		$stmtgetMember = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'SYSChannelMembers'");
				$stmtgetMember->execute();	
				$result= $stmtgetMember->fetchAll();	
				$keylist="";

				foreach ($jsonbody as $key => $value){
					$val=getInsertQueryMember($key,$value,$result);
						if(count($val)!=0){
								if(empty($keylist)){
									$keylist.=$val['key']." = ".$val['value'];	
								}else{
									$keylist.=", ".$val['key']." = ".$val['value'];
								}
							
						}
				}

				$updateMember="UPDATE SYSChannelMembers SET $keylist, updatetime = CURRENT_TIMESTAMP WHERE id = '$memID' and status = 1";
				$stmtupdate = $dbLocal->prepare($updateMember);
				$stmtupdate->execute();
				echo "success";
	}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-197"}';
				$checkFlag = false;
			}
	
     

}

function deleteMember($memID){
	global $dbnamelocal; 
	$dbLocal = getdbhimirrorqa();
	$checkQuery = "SELECT * from SYSChannelMembers WHERE `id` = '".$memID."'";
		$stmt = $dbLocal->query($checkQuery);
		$stmt->execute();
		$result= $stmt->fetchAll();
		$countDup = count($result);

		try{
			if(!empty($countDup)){
				$DeleteQuery = "UPDATE SYSChannelMembers SET status = 0 WHERE `id` = '".$memID."'";
				$stmtdelete = $dbLocal->prepare($DeleteQuery);
				if($stmtdelete->execute()){
					echo "Successfully deleted";
				}else{
					echo '{"response":"Failed to Delete","errcode":"b2ber009"}';
				}
			}else{
				echo '{"response":"	No Result","errcode":"b2ber004"}';
			}
		}catch(PDOException $e){
			echo '{"response":"PDO Exception","errcode":"b2ber003-227"}';
		}
	
     

}

function getInsertQueryMember($key,$value,$columns){
	$returnarray= array();

	foreach ($columns as $columnskey => $columnsvalue){
			
			if($columnsvalue['COLUMN_NAME']==$key){
					$returnarray['key']=$key;
					
					if(strpos($columnsvalue['DATA_TYPE'],"int") !== false){
						$returnarray['value']= checkEmptyInt2($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"float") !== false){
						$returnarray['value']= checkEmptyInt2($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"double") !== false){
						$returnarray['value']= checkEmptyInt2($value);
					}else if(strpos($columnsvalue['DATA_TYPE'],"var") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty2($value)."'";
						else
							$returnarray['value']=$value;
					}else if(strpos($columnsvalue['DATA_TYPE'],"char") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty2($value)."'";
						else
							$returnarray['value']=$value;
					}else if(strpos($columnsvalue['DATA_TYPE'],"text") !== false){
						if(!is_array($value))
							$returnarray['value']="'".checkEmpty2($value)."'";
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

function checkEmptyInt2($value){

	$value = str_replace("'", "''", $value);
	if(!empty($value)){
		$finalValue = $value;
	}else{
		$finalValue = 0;
	}

	return $finalValue;
}

function checkEmpty2($value){

	$value = str_replace("'", "''", $value);
	if(!empty($value)){
		$finalValue = $value;
	}else{
		$finalValue = "N/A";
	}

	return $finalValue;
}

function checkMemberIfExist($body,$channelid){
	$dbLocal = getdbhimirrorqa();
	foreach ($body as $key => $value){
		if ($key!="password" && $key!="gender" && $key!="birthday") {
			$stmtgetMember = $dbLocal->prepare("SELECT * FROM SYSChannelMembers WHERE syschannelprofiles_id = '$channelid' AND $key = '$value'");
			$stmtgetMember->execute();	
			$result= $stmtgetMember->fetchAll();
			if (count($result)> 0) {
				return true;
			}
		}
		
	}
	return false;
}

?>