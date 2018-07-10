<?php

function getGroupList($body){
	$selectQuery = "SELECT SYSChannelBrand.name as brand_name,SYSChannelProfiles.name,SYSChannelProfiles.id,SYSChannelProfiles.status,Mirror_Devices,Member_Amount,SYSChannelProfiles.country,SYSChannelProfiles.createtime,SYSChannelProfiles.available_space FROM `SYSChannelProfiles` INNER JOIN SYSChannelBrand on SYSChannelBrand.id=SYSChannelProfiles.syschannelbrand_id INNER JOIN (SELECT SYSChannelDevices.syschannelprofiles_id,count(SYSChannelDevices.id) as Mirror_Devices FROM `SYSChannelDevices` GROUP by SYSChannelDevices.syschannelprofiles_id) as Devices on SYSChannelProfiles.id=Devices.syschannelprofiles_id LEFT JOIN (SELECT SYSChannelMembers.syschannelprofiles_id,count(SYSChannelMembers.id) as Member_Amount FROM `SYSChannelMembers` GROUP BY SYSChannelMembers.syschannelprofiles_id) as Members on Members.syschannelprofiles_id=SYSChannelProfiles.id";




	$whereClause = '';
		foreach ($body as $key => $value) {
			switch ($key) {
				case 'brand_name':
					if(empty($whereClause))
						$whereClause .= " SYSChannelBrand.name ='$value' ";
					else
						$whereClause .= " AND SYSChannelBrand.name ='$value' ";
					break;
				case 'name':
					if(empty($whereClause))
						$whereClause .= " name ='$value' ";
					else
						$whereClause .= " AND name ='$value' ";
					break;
				case 'status':
					if(empty($whereClause))
						$whereClause .= " status =$value ";
					else
						$whereClause .= " AND status =$value ";
					break;		
				case 'Mirror_Devices':
					if(empty($whereClause))
						$whereClause .= " Mirror_Devices =$value ";
					else
						$whereClause .= " AND Mirror_Devices  =$value ";
					break;	
				case 'Member_Amount':
					if(empty($whereClause))
						$whereClause .= " Member_Amount =$value ";
					else
						$whereClause .= " AND Member_Amount  =$value ";
					break;	
				case 'country':
					if(empty($whereClause))
						$whereClause .= " country ='$value' ";
					else
						$whereClause .= " AND country  ='$value' ";
					break;	
				case 'available_space':
					if(empty($whereClause))
						$whereClause .= " available_space ='$value' ";
					else
						$whereClause .= " AND available_space  ='$value' ";
					break;	
				case 'startdate':
					if(empty($whereClause))
						$whereClause .= " createtime >='$value' ";
					else
						$whereClause .= " AND createtime >='$value' ";
					break;
				case 'enddate':
					if(empty($whereClause))
						$whereClause .= " createtime <='$value' ";
					else
						$whereClause .= " AND createtime <='$value' ";
					break;		

				default:
					# code...
					break;
			}
		}
	if(!empty($whereClause)){
		$selectQuery .=" WHERE ".$whereClause;
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
			echo "execute error";
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-94"}';
		
	}
}
function GetChannelProfileFilterCategory(){
	try{
		$dbLocal = getdbhimirrorqa();
		$channelDeviceArr = array();
		$stmtget = $dbLocal->prepare("SELECT DISTINCT country FROM SYSChannelProfiles");
		$stmtget->execute();	
		$result= $stmtget->fetchAll();
		$channelDeviceArr['country_list']=$result;
		$stmtget = $dbLocal->prepare("SELECT DISTINCT name FROM SYSChannelBrand");
		$stmtget->execute();	
		$result2= $stmtget->fetchAll();
		$channelDeviceArr['brand_list']=$result2;
		$stmtget = $dbLocal->prepare("SELECT DISTINCT name FROM SYSChannelProfiles");
		$stmtget->execute();	
		$result3= $stmtget->fetchAll();
		$channelDeviceArr['GroupName_list']=$result3;
		
		echo json_encode($channelDeviceArr);
	}
	catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-125"}';
	}
}
?>