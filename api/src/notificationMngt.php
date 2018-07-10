<?php

include("globalLocal.php");

function getNotifications($channelid){

	$notifArray = array();
	$dbLocal = getdbhimirrorqa();

	try{
		$getadlist = "SELECT COUNT(id) as advertisementCount from USRAdvertisements WHERE syschannelprofiles_id = '$channelid'";
		$stmtgetad = $dbLocal->query($getadlist);
		$stmtgetad->execute();
		$result= $stmtgetad->fetchAll();
		$adCount = $result[0]['advertisementCount'];
		$notifArray['adCount'] = $adCount;
	}catch(PDOException $e){
		echo "Failed 18";
	}

	try{
		$getdlist = "SELECT COUNT(id) as digitalCount from USRDigitalSignages WHERE syschannelprofiles_id = '$channelid'";
		$stmtgetdigital = $dbLocal->query($getdlist);
		$stmtgetdigital->execute();
		$result2= $stmtgetdigital->fetchAll();
		$digitalCount = $result2[0]['digitalCount'];
		$notifArray['digitalCount'] = $digitalCount;
	}catch(PDOException $e){
		echo "Failed 29";
	}

	try{
		$getprodlist = "SELECT COUNT(id) as productCount from USRChannelProducts WHERE syschannelprofiles_id = '$channelid'";
		$stmtgetprod = $dbLocal->query($getprodlist);
		$stmtgetprod->execute();
		$result3= $stmtgetprod->fetchAll();
		$productCount = $result3[0]['productCount'];
		$notifArray['productCount'] = $productCount;
	}catch(PDOException $e){
		echo "Failed 40";
	}

	try{
		$factorsArray = array("home","redspot","wrinkle","fineline","texture","eyeblow","pore");
		foreach($factorsArray as $key=>$value){
			$getFactor = "SELECT COUNT(USRChannelProductRecommendationsPackages.id) as count_$value FROM `USRChannelProductRecommendationsPackagesMappingFactor` INNER JOIN USRChannelProductRecommendationsPackages ON USRChannelProductRecommendationsPackagesMappingFactor.packages_id = USRChannelProductRecommendationsPackages.id WHERE USRChannelProductRecommendationsPackages.syschannelprofiles_id = '$channelid' AND USRChannelProductRecommendationsPackagesMappingFactor.factor_name LIKE '%".$value."%'";
			$stmtgetfactor = $dbLocal->query($getFactor);
			$stmtgetfactor->execute();
			$result3= $stmtgetfactor->fetchAll();
			for($i=0; $i<count($result3);$i++){
				foreach($result3[$i] as $key2=>$value2){
					$notifArray[$key2] = $value2;
				}
			}
		}
	}catch(PDOException $e){
		echo "Failed 57";
	}
	
	echo json_encode($notifArray);
}
?>