<?php

function hiskintotallist($channelid){
	$dbLocal 	= getdbhimirrorqa();
	$selectQuery = "SELECT USRHiSkin.id AS UserID,eyeAreaHydration.eyeAreaHydrationValue, eyeAreaMelanin.eyeAreaMelaninValue, 
eyeAreaOxygen.eyeAreaOxygenValue, foreheadHydration.foreheadHydrationValue, foreheadMelanin.foreheadMelaninValue, 
foreheadOxygen.foreheadOxygenValue, lowerCheekHydration.lowerCheekHydrationValue, lowerCheekMelanin.lowerCheekMelaninValue, 
lowerCheekOxygen.lowerCheekOxygenValue, neckHydration.neckHydrationValue, neckMelanin.neckMelaninValue, 
neckOxygen.neckOxygenValue, upperCheekHydration.upperCheekHydrationValue, upperCheekMelanin.upperCheekMelaninValue, 
upperCheekOxygen.upperCheekOxygenValue, USRHiSkin.insert_time, USRHiSkin.syschannelprofiles_id, SYSChannelMembers.account,
SYSChannelMembers.birthday,SYSChannelMembers.gender,SYSChannelMembers.syschannelprofiles_id FROM `USRHiSkin` 

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaHydrationID, USRHiSkinRaw.hydration AS eyeAreaHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaHydration ON 
eyeAreaHydration.eyeAreaHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaMelaninID, USRHiSkinRaw.melanin AS eyeAreaMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaMelanin ON 
eyeAreaMelanin.eyeAreaMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaOxygenID, USRHiSkinRaw.oxygen AS eyeAreaOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaOxygen ON 
eyeAreaOxygen.eyeAreaOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadHydrationID, USRHiSkinRaw.hydration AS foreheadHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadHydration ON 
foreheadHydration.foreheadHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadMelaninID, USRHiSkinRaw.melanin AS foreheadMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadMelanin ON 
foreheadMelanin.foreheadMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadOxygenID, USRHiSkinRaw.oxygen AS foreheadOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadOxygen ON 
foreheadOxygen.foreheadOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekHydrationID, USRHiSkinRaw.hydration AS lowerCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekHydration ON 
lowerCheekHydration.lowerCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekMelaninID, USRHiSkinRaw.melanin AS lowerCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekMelanin ON 
lowerCheekMelanin.lowerCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekOxygenID, USRHiSkinRaw.oxygen AS lowerCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekOxygen ON 
lowerCheekOxygen.lowerCheekOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckHydrationID, USRHiSkinRaw.hydration AS neckHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckHydration ON 
neckHydration.neckHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckMelaninID, USRHiSkinRaw.melanin AS neckMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckMelanin ON 
neckMelanin.neckMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckOxygenID, USRHiSkinRaw.oxygen AS neckOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckOxygen ON 
neckOxygen.neckOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekHydrationID, USRHiSkinRaw.hydration AS upperCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekHydration ON 
upperCheekHydration.upperCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekMelaninID, USRHiSkinRaw.melanin AS upperCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekMelanin ON 
upperCheekMelanin.upperCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekOxygenID, USRHiSkinRaw.oxygen AS upperCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekOxygen ON 
upperCheekOxygen.upperCheekOxygenID = USRHiSkin.id

INNER JOIN SYSChannelMembers on USRHiSkin.syschannelprofiles_id=SYSChannelMembers.id where  insert_time>='2016-01-01' AND syschannelprofiles_id='$channelid' ";
	try{
		$stmt = $dbLocal->prepare($selectQuery);
		if($stmt->execute()){
			$res = $stmt->fetchAll();
			$numofdata = $stmt->rowCount();
			if($numofdata > 0){
				echo json_encode($res);
			}else
				echo "no result";
		}else
			echo "execute error";
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-86"}';
	}

}

function hiskintotalCount($channelid){
	$dbLocal 	= getdbhimirrorqa();
	$selectQuery = "SELECT COUNT(*) AS TotalCount FROM `USRHiSkin` 

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaHydrationID, USRHiSkinRaw.hydration AS eyeAreaHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaHydration ON 
eyeAreaHydration.eyeAreaHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaMelaninID, USRHiSkinRaw.melanin AS eyeAreaMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaMelanin ON 
eyeAreaMelanin.eyeAreaMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaOxygenID, USRHiSkinRaw.oxygen AS eyeAreaOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaOxygen ON 
eyeAreaOxygen.eyeAreaOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadHydrationID, USRHiSkinRaw.hydration AS foreheadHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadHydration ON 
foreheadHydration.foreheadHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadMelaninID, USRHiSkinRaw.melanin AS foreheadMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadMelanin ON 
foreheadMelanin.foreheadMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadOxygenID, USRHiSkinRaw.oxygen AS foreheadOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadOxygen ON 
foreheadOxygen.foreheadOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekHydrationID, USRHiSkinRaw.hydration AS lowerCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekHydration ON 
lowerCheekHydration.lowerCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekMelaninID, USRHiSkinRaw.melanin AS lowerCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekMelanin ON 
lowerCheekMelanin.lowerCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekOxygenID, USRHiSkinRaw.oxygen AS lowerCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekOxygen ON 
lowerCheekOxygen.lowerCheekOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckHydrationID, USRHiSkinRaw.hydration AS neckHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckHydration ON 
neckHydration.neckHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckMelaninID, USRHiSkinRaw.melanin AS neckMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckMelanin ON 
neckMelanin.neckMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckOxygenID, USRHiSkinRaw.oxygen AS neckOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckOxygen ON 
neckOxygen.neckOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekHydrationID, USRHiSkinRaw.hydration AS upperCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekHydration ON 
upperCheekHydration.upperCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekMelaninID, USRHiSkinRaw.melanin AS upperCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekMelanin ON 
upperCheekMelanin.upperCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekOxygenID, USRHiSkinRaw.oxygen AS upperCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekOxygen ON 
upperCheekOxygen.upperCheekOxygenID = USRHiSkin.id

INNER JOIN SYSChannelMembers on USRHiSkin.syschannelprofiles_id=SYSChannelMembers.id where  insert_time>='2016-01-01' AND syschannelprofiles_id='$channelid' ";
	try{
		$stmt = $dbLocal->prepare($selectQuery);
		if($stmt->execute()){
			$result = $stmt->fetchAll();
			echo $result[0]['TotalCount'];
			
		}else{
			echo "0";
		}
		
	}catch(PDOException $e){
		echo "0";
	}

}

function hiskinfilterlist($body,$channelid){
	$dbLocal 	= getdbhimirrorqa();
	$selectQuery = "SELECT USRHiSkin.id AS UserID,eyeAreaHydration.eyeAreaHydrationValue, eyeAreaMelanin.eyeAreaMelaninValue, 
eyeAreaOxygen.eyeAreaOxygenValue, foreheadHydration.foreheadHydrationValue, foreheadMelanin.foreheadMelaninValue, 
foreheadOxygen.foreheadOxygenValue, lowerCheekHydration.lowerCheekHydrationValue, lowerCheekMelanin.lowerCheekMelaninValue, 
lowerCheekOxygen.lowerCheekOxygenValue, neckHydration.neckHydrationValue, neckMelanin.neckMelaninValue, 
neckOxygen.neckOxygenValue, upperCheekHydration.upperCheekHydrationValue, upperCheekMelanin.upperCheekMelaninValue, 
upperCheekOxygen.upperCheekOxygenValue, USRHiSkin.insert_time, USRHiSkin.syschannelprofiles_id, SYSChannelMembers.account,
SYSChannelMembers.birthday,SYSChannelMembers.gender,SYSChannelMembers.syschannelprofiles_id FROM `USRHiSkin` 

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaHydrationID, USRHiSkinRaw.hydration AS eyeAreaHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaHydration ON 
eyeAreaHydration.eyeAreaHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaMelaninID, USRHiSkinRaw.melanin AS eyeAreaMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaMelanin ON 
eyeAreaMelanin.eyeAreaMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaOxygenID, USRHiSkinRaw.oxygen AS eyeAreaOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaOxygen ON 
eyeAreaOxygen.eyeAreaOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadHydrationID, USRHiSkinRaw.hydration AS foreheadHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadHydration ON 
foreheadHydration.foreheadHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadMelaninID, USRHiSkinRaw.melanin AS foreheadMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadMelanin ON 
foreheadMelanin.foreheadMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadOxygenID, USRHiSkinRaw.oxygen AS foreheadOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadOxygen ON 
foreheadOxygen.foreheadOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekHydrationID, USRHiSkinRaw.hydration AS lowerCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekHydration ON 
lowerCheekHydration.lowerCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekMelaninID, USRHiSkinRaw.melanin AS lowerCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekMelanin ON 
lowerCheekMelanin.lowerCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekOxygenID, USRHiSkinRaw.oxygen AS lowerCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekOxygen ON 
lowerCheekOxygen.lowerCheekOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckHydrationID, USRHiSkinRaw.hydration AS neckHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckHydration ON 
neckHydration.neckHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckMelaninID, USRHiSkinRaw.melanin AS neckMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckMelanin ON 
neckMelanin.neckMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckOxygenID, USRHiSkinRaw.oxygen AS neckOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckOxygen ON 
neckOxygen.neckOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekHydrationID, USRHiSkinRaw.hydration AS upperCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekHydration ON 
upperCheekHydration.upperCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekMelaninID, USRHiSkinRaw.melanin AS upperCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekMelanin ON 
upperCheekMelanin.upperCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekOxygenID, USRHiSkinRaw.oxygen AS upperCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekOxygen ON 
upperCheekOxygen.upperCheekOxygenID = USRHiSkin.id

INNER JOIN SYSChannelMembers on USRHiSkin.syschannelprofiles_id=SYSChannelMembers.id where syschannelprofiles_id='$channelid' AND ";
	$whereClause = '';
	foreach ($body as $key => $value) {
		switch ($key) {
			case 'measurestartDate':
				$whereClause .= "insert_time >='$value' ";
				break;
			case 'measureendDate':
				$whereClause .= "AND insert_time <= '$value' ";
				break;
			case 'eyeareahydrationmin':
				$whereClause .= "AND eyeAreaHydrationValue >='$value' ";
				break;
			case 'eyeareahydrationmax':
				$whereClause .= "AND eyeAreaHydrationValue <= '$value' ";
				break;
			case 'foreheadhydrationmin':
				$whereClause .= "AND foreheadHydrationValue >='$value' ";
				break;
			case 'foreheadhydrationmax':
				$whereClause .= "AND foreheadHydrationValue <= '$value' ";
				break;
			case 'lowercheekhydrationmin':
				$whereClause .= "AND lowerCheekHydrationValue >='$value' ";
				break;
			case 'lowercheekhydrationmax':
				$whereClause .= "AND lowerCheekHydrationValue <= '$value' ";
				break;
			case 'neckhydrationmin':
				$whereClause .= "AND neckHydrationValue >='$value' ";
				break;
			case 'neckhydrationmax':
				$whereClause .= "AND neckHydrationValue <= '$value' ";
				break;
			case 'upcheekhydrationmin':
				$whereClause .= "AND upperCheekHydrationValue >='$value' ";
				break;
			case 'upcheekhydrationmax':
				$whereClause .= "AND upperCheekHydrationValue <= '$value' ";
				break;
			case 'eyeareamelaninmin':
				$whereClause .= "AND eyeAreaMelaninValue >='$value' ";
				break;
			case 'eyeareamelaninmax':
				$whereClause .= "AND eyeAreaMelaninValue <= '$value' ";
				break;
			case 'foreheadmelaninmin':
				$whereClause .= "AND foreheadMelaninValue >='$value' ";
				break;
			case 'foreheadmelaninmax':
				$whereClause .= "AND foreheadMelaninValue <= '$value' ";
				break;
			case 'lowercheekmelaninmin':
				$whereClause .= "AND lowerCheekMelaninValue >='$value' ";
				break;
			case 'lowercheekmelaninmax':
				$whereClause .= "AND lowerCheekMelaninValue <= '$value' ";
				break;
			case 'neckmelaninmin':
				$whereClause .= "AND neckMelaninValue >='$value' ";
				break;
			case 'neckmelaninmax':
				$whereClause .= "AND neckMelaninValue <= '$value' ";
				break;
			case 'upcheekmelaninmin':
				$whereClause .= "AND upperCheekMelaninValue >='$value' ";
				break;
			case 'upcheekmelaninmax':
				$whereClause .= "AND upperCheekMelaninValue <= '$value' ";
				break;
			case 'eyeareaoxygenmin':
				$whereClause .= "AND eyeAreaOxygenValue >='$value' ";
				break;
			case 'eyeareaoxygenmax':
				$whereClause .= "AND eyeAreaOxygenValue <= '$value' ";
				break;
			case 'foreheadoxygenmin':
				$whereClause .= "AND foreheadOxygenValue >='$value' ";
				break;
			case 'foreheadoxygenmax':
				$whereClause .= "AND foreheadOxygenValue <= '$value' ";
				break;
			case 'lowercheekoxygenmin':
				$whereClause .= "AND lowerCheekOxygenValue >='$value' ";
				break;
			case 'lowercheekoxygenmax':
				$whereClause .= "AND lowerCheekOxygenValue <= '$value' ";
				break;
			case 'neckoxygenmin':
				$whereClause .= "AND neckOxygenValue >='$value' ";
				break;
			case 'neckoxygenmax':
				$whereClause .= "AND neckOxygenValue <= '$value' ";
				break;
			case 'upcheekoxygenmin':
				$whereClause .= "AND upperCheekOxygenValue >='$value' ";
				break;
			case 'upcheekoxygenmax':
				$whereClause .= "AND upperCheekOxygenValue <= '$value' ";
				break;
			default:
				# code...
				break;
		}

	}
	$selectQuery .= $whereClause;
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
			echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-362"}';
	}

}
function gethiskinlistbyIdentifier($identifier,$channelid){
	$dbLocal 	= getdbhimirrorqa();
	$selectQuery = "SELECT USRHiSkin.id AS UserID,eyeAreaHydration.eyeAreaHydrationValue, eyeAreaMelanin.eyeAreaMelaninValue, 
eyeAreaOxygen.eyeAreaOxygenValue, foreheadHydration.foreheadHydrationValue, foreheadMelanin.foreheadMelaninValue, 
foreheadOxygen.foreheadOxygenValue, lowerCheekHydration.lowerCheekHydrationValue, lowerCheekMelanin.lowerCheekMelaninValue, 
lowerCheekOxygen.lowerCheekOxygenValue, neckHydration.neckHydrationValue, neckMelanin.neckMelaninValue, 
neckOxygen.neckOxygenValue, upperCheekHydration.upperCheekHydrationValue, upperCheekMelanin.upperCheekMelaninValue, 
upperCheekOxygen.upperCheekOxygenValue, USRHiSkin.insert_time, USRHiSkin.syschannelprofiles_id, SYSChannelMembers.account,
SYSChannelMembers.birthday,SYSChannelMembers.gender,SYSChannelMembers.syschannelprofiles_id FROM `USRHiSkin` 

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaHydrationID, USRHiSkinRaw.hydration AS eyeAreaHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaHydration ON 
eyeAreaHydration.eyeAreaHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaMelaninID, USRHiSkinRaw.melanin AS eyeAreaMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaMelanin ON 
eyeAreaMelanin.eyeAreaMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS eyeAreaOxygenID, USRHiSkinRaw.oxygen AS eyeAreaOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'eyeArea') as eyeAreaOxygen ON 
eyeAreaOxygen.eyeAreaOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadHydrationID, USRHiSkinRaw.hydration AS foreheadHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadHydration ON 
foreheadHydration.foreheadHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadMelaninID, USRHiSkinRaw.melanin AS foreheadMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadMelanin ON 
foreheadMelanin.foreheadMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS foreheadOxygenID, USRHiSkinRaw.oxygen AS foreheadOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'forehead') as foreheadOxygen ON 
foreheadOxygen.foreheadOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekHydrationID, USRHiSkinRaw.hydration AS lowerCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekHydration ON 
lowerCheekHydration.lowerCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekMelaninID, USRHiSkinRaw.melanin AS lowerCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekMelanin ON 
lowerCheekMelanin.lowerCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS lowerCheekOxygenID, USRHiSkinRaw.oxygen AS lowerCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'lowerCheek') as lowerCheekOxygen ON 
lowerCheekOxygen.lowerCheekOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckHydrationID, USRHiSkinRaw.hydration AS neckHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckHydration ON 
neckHydration.neckHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckMelaninID, USRHiSkinRaw.melanin AS neckMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckMelanin ON 
neckMelanin.neckMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS neckOxygenID, USRHiSkinRaw.oxygen AS neckOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'neck') as neckOxygen ON 
neckOxygen.neckOxygenID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekHydrationID, USRHiSkinRaw.hydration AS upperCheekHydrationValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekHydration ON 
upperCheekHydration.upperCheekHydrationID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekMelaninID, USRHiSkinRaw.melanin AS upperCheekMelaninValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekMelanin ON 
upperCheekMelanin.upperCheekMelaninID = USRHiSkin.id

INNER JOIN (SELECT USRHiSkinRaw.hiksin_id AS upperCheekOxygenID, USRHiSkinRaw.oxygen AS upperCheekOxygenValue 
 FROM USRHiSkinRaw WHERE USRHiSkinRaw.detect_area = 'upperCheek') as upperCheekOxygen ON 
upperCheekOxygen.upperCheekOxygenID = USRHiSkin.id

INNER JOIN SYSChannelMembers on USRHiSkin.syschannelprofiles_id=SYSChannelMembers.id where account='$identifier' OR id_card='$identifier' OR phone_number='$identifier' AND syschannelprofiles_id='$channelid'";
	
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
			echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-450"}';
	}

}
?>