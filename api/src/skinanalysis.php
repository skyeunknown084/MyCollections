<?php

function skinanalysislist($body,$channelid){
	$selectQuery = "SELECT * FROM `USRSkinAnalysis` INNER JOIN (SELECT skincare_id as acneid,type_value as AcnePercentage from USRSkinAnalysisRaw where factor='acne' AND detect_area='total' AND type_name='percentage') as acne on acne.acneid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as blackspotid,type_value as DarkSpotsPercentage from USRSkinAnalysisRaw where factor='blackspot' AND detect_area='total' AND type_name='percentage') as blackspot on blackspot.blackspotid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as darkcircleid,type_value as DarkCirclesPercentage from USRSkinAnalysisRaw where factor='darkcircle' AND detect_area='total' AND type_name='percentage') as darkcircle on darkcircle.darkcircleid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as fineid,type_value as FinelinesPercentage from USRSkinAnalysisRaw where factor='fine' AND detect_area='total' AND type_name='percentage') as fine on fine.fineid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as poreid,type_value as PoresPercentage from USRSkinAnalysisRaw where factor='pore' AND detect_area='total' AND type_name='percentage') as pore on pore.poreid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as textureid,type_value as TexturePercentage from USRSkinAnalysisRaw where factor='texture' AND detect_area='total' AND type_name='percentage') as texture on texture.textureid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as wrinkleid,type_value as WrinklesPercentage from USRSkinAnalysisRaw where factor='wrinkle' AND detect_area='total' AND type_name='percentage') as wrinkle on wrinkle.wrinkleid=USRSkinAnalysis.id INNER JOIN SYSChannelMembers on USRSkinAnalysis.syschannelmembers_id=SYSChannelMembers.id where syschannelmembers_id='$channelid' AND ";




	$whereClause = '';
		foreach ($body as $key => $value) {
			switch ($key) {
				case 'measurestartDate':
					$whereClause .= "insert_time>='$value' ";
					break;
				case 'measureendDate':
					$whereClause .= "AND insert_time <= '$value' ";
					break;
				
				case 'redspotsmin':
					$whereClause .= "AND AcnePercentage > '$value' ";
					break;
				case 'redspotsmax':
					$whereClause .= "AND AcnePercentage <= '$value' ";
					break;
				case 'blackspotmin':
					$whereClause .= "AND DarkSpotsPercentage > '$value' ";
					break;
				case 'blackspotmax':
					$whereClause .= "AND DarkSpotsPercentage <= '$value' ";
					break;
				case 'eyebrowmin':
					$whereClause .= "AND DarkCirclesPercentage > '$value' ";
					break;
				case 'eyebrowmax':
					$whereClause .= "AND DarkCirclesPercentage <= '$value' ";
					break;
				case 'finelinemin':
					$whereClause .= "AND FinelinesPercentage > '$value' ";
					break;
				case 'finelinemax':
					$whereClause .= "AND FinelinesPercentage <= '$value' ";
					break;
				case 'poresmin':
					$whereClause .= "AND PoresPercentage > '$value' ";
					break;
				case 'poresmax':
					$whereClause .= "AND PoresPercentage <= '$value' ";
					break;
				case 'texturemin':
					$whereClause .= "AND TexturePercentage > '$value' ";
					break;
				case 'texturemax':
					$whereClause .= "AND TexturePercentage <= '$value' ";
					break;
				case 'wrinklemin':
					$whereClause .= "AND WrinklesPercentage > '$value' ";
					break;
				case 'wrinklemax':
					$whereClause .= "AND WrinklesPercentage <= '$value' ";
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
			echo "execute error";
	}catch(PDOException $e){
		echo '{"response":"PDO Exception","errcode":"b2ber003-94"}';
		
	}
}

function skinanalysistotal($channelid){
	$selectQuery="SELECT COUNT(*) as TotalCount FROM `USRSkinAnalysis` INNER JOIN (SELECT skincare_id as acneid,type_value as AcnePercentage from USRSkinAnalysisRaw where factor='acne' AND detect_area='total' AND type_name='percentage') as acne on acne.acneid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as blackspotid,type_value as DarkSpotsPercentage from USRSkinAnalysisRaw where factor='blackspot' AND detect_area='total' AND type_name='percentage') as blackspot on blackspot.blackspotid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as darkcircleid,type_value as DarkCirclesPercentage from USRSkinAnalysisRaw where factor='darkcircle' AND detect_area='total' AND type_name='percentage') as darkcircle on darkcircle.darkcircleid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as fineid,type_value as FinelinesPercentage from USRSkinAnalysisRaw where factor='fine' AND detect_area='total' AND type_name='percentage') as fine on fine.fineid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as poreid,type_value as PoresPercentage from USRSkinAnalysisRaw where factor='pore' AND detect_area='total' AND type_name='percentage') as pore on pore.poreid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as textureid,type_value as TexturePercentage from USRSkinAnalysisRaw where factor='texture' AND detect_area='total' AND type_name='percentage') as texture on texture.textureid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as wrinkleid,type_value as WrinklesPercentage from USRSkinAnalysisRaw where factor='wrinkle' AND detect_area='total' AND type_name='percentage') as wrinkle on wrinkle.wrinkleid=USRSkinAnalysis.id where  insert_time>='2016-01-01' AND syschannelmembers_id='$channelid'";
	

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
		echo "0";
	}
}

function skinanalysistotallist($channelid){
	$selectQuery = "SELECT * FROM `USRSkinAnalysis` INNER JOIN (SELECT skincare_id as acneid,type_value as AcnePercentage from USRSkinAnalysisRaw where factor='acne' AND detect_area='total' AND type_name='percentage') as acne on acne.acneid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as blackspotid,type_value as DarkSpotsPercentage from USRSkinAnalysisRaw where factor='blackspot' AND detect_area='total' AND type_name='percentage') as blackspot on blackspot.blackspotid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as darkcircleid,type_value as DarkCirclesPercentage from USRSkinAnalysisRaw where factor='darkcircle' AND detect_area='total' AND type_name='percentage') as darkcircle on darkcircle.darkcircleid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as fineid,type_value as FinelinesPercentage from USRSkinAnalysisRaw where factor='fine' AND detect_area='total' AND type_name='percentage') as fine on fine.fineid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as poreid,type_value as PoresPercentage from USRSkinAnalysisRaw where factor='pore' AND detect_area='total' AND type_name='percentage') as pore on pore.poreid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as textureid,type_value as TexturePercentage from USRSkinAnalysisRaw where factor='texture' AND detect_area='total' AND type_name='percentage') as texture on texture.textureid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as wrinkleid,type_value as WrinklesPercentage from USRSkinAnalysisRaw where factor='wrinkle' AND detect_area='total' AND type_name='percentage') as wrinkle on wrinkle.wrinkleid=USRSkinAnalysis.id INNER JOIN SYSChannelMembers on USRSkinAnalysis.syschannelmembers_id=SYSChannelMembers.id where  insert_time>='2016-01-01' AND syschannelmembers_id='$channelid'" ;

	$dbLocal 	= getdbhimirrorqa();
	// echo $selectQuery;
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
		echo '{"response":"PDO Exception","errcode":"b2ber003-163"}';
	}
}

function skinanalysisfilter($body,$channelid){
	$selectQuery = "SELECT COUNT(*) as TotalCount FROM `USRSkinAnalysis` INNER JOIN (SELECT skincare_id as acneid,type_value as AcnePercentage from USRSkinAnalysisRaw where factor='acne' AND detect_area='total' AND type_name='percentage') as acne on acne.acneid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as blackspotid,type_value as DarkSpotsPercentage from USRSkinAnalysisRaw where factor='blackspot' AND detect_area='total' AND type_name='percentage') as blackspot on blackspot.blackspotid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as darkcircleid,type_value as DarkCirclesPercentage from USRSkinAnalysisRaw where factor='darkcircle' AND detect_area='total' AND type_name='percentage') as darkcircle on darkcircle.darkcircleid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as fineid,type_value as FinelinesPercentage from USRSkinAnalysisRaw where factor='fine' AND detect_area='total' AND type_name='percentage') as fine on fine.fineid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as poreid,type_value as PoresPercentage from USRSkinAnalysisRaw where factor='pore' AND detect_area='total' AND type_name='percentage') as pore on pore.poreid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as textureid,type_value as TexturePercentage from USRSkinAnalysisRaw where factor='texture' AND detect_area='total' AND type_name='percentage') as texture on texture.textureid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as wrinkleid,type_value as WrinklesPercentage from USRSkinAnalysisRaw where factor='wrinkle' AND detect_area='total' AND type_name='percentage') as wrinkle on wrinkle.wrinkleid=USRSkinAnalysis.id INNER JOIN SYSChannelMembers on USRSkinAnalysis.syschannelmembers_id=SYSChannelMembers.id where syschannelmembers_id='$channelid' AND ";
	$whereClause = '';
		foreach ($body as $key => $value) {
			switch ($key) {
				case 'measurestartDate':
					$whereClause .= "insert_time>='$value' ";
					break;
				case 'measureendDate':
					$whereClause .= "AND insert_time <= '$value' ";
					break;
				
				case 'redspotsmin':
					$whereClause .= "AND AcnePercentage > '$value' ";
					break;
				case 'redspotsmax':
					$whereClause .= "AND AcnePercentage <= '$value' ";
					break;
				case 'blackspotmin':
					$whereClause .= "AND DarkSpotsPercentage > '$value' ";
					break;
				case 'blackspotmax':
					$whereClause .= "AND DarkSpotsPercentage <= '$value' ";
					break;
				case 'eyebrowmin':
					$whereClause .= "AND DarkCirclesPercentage > '$value' ";
					break;
				case 'eyebrowmax':
					$whereClause .= "AND DarkCirclesPercentage <= '$value' ";
					break;
				case 'finelinemin':
					$whereClause .= "AND FinelinesPercentage > '$value' ";
					break;
				case 'finelinemax':
					$whereClause .= "AND FinelinesPercentage <= '$value' ";
					break;
				case 'poresmin':
					$whereClause .= "AND PoresPercentage > '$value' ";
					break;
				case 'poresmax':
					$whereClause .= "AND PoresPercentage <= '$value' ";
					break;
				case 'texturemin':
					$whereClause .= "AND TexturePercentage > '$value' ";
					break;
				case 'texturemax':
					$whereClause .= "AND TexturePercentage <= '$value' ";
					break;
				case 'wrinklemin':
					$whereClause .= "AND WrinklesPercentage > '$value' ";
					break;
				case 'wrinklemax':
					$whereClause .= "AND WrinklesPercentage <= '$value' ";
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
			$result = $stmt->fetchAll();
			echo $result[0]['TotalCount'];
			
		}else{
			echo "0";
		}
		
	}catch(PDOException $e){
		echo "0";
	}
}



function getskindatalistbyIdentifier($identifier,$channelid){
	$dbLocal 	= getdbhimirrorqa();
	$selectQuery = "SELECT * FROM `USRSkinAnalysis` INNER JOIN (SELECT skincare_id as acneid,type_value as AcnePercentage from USRSkinAnalysisRaw where factor='acne' AND detect_area='total' AND type_name='percentage') as acne on acne.acneid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as blackspotid,type_value as DarkSpotsPercentage from USRSkinAnalysisRaw where factor='blackspot' AND detect_area='total' AND type_name='percentage') as blackspot on blackspot.blackspotid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as darkcircleid,type_value as DarkCirclesPercentage from USRSkinAnalysisRaw where factor='darkcircle' AND detect_area='total' AND type_name='percentage') as darkcircle on darkcircle.darkcircleid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as fineid,type_value as FinelinesPercentage from USRSkinAnalysisRaw where factor='fine' AND detect_area='total' AND type_name='percentage') as fine on fine.fineid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as poreid,type_value as PoresPercentage from USRSkinAnalysisRaw where factor='pore' AND detect_area='total' AND type_name='percentage') as pore on pore.poreid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as textureid,type_value as TexturePercentage from USRSkinAnalysisRaw where factor='texture' AND detect_area='total' AND type_name='percentage') as texture on texture.textureid=USRSkinAnalysis.id

INNER JOIN (SELECT skincare_id as wrinkleid,type_value as WrinklesPercentage from USRSkinAnalysisRaw where factor='wrinkle' AND detect_area='total' AND type_name='percentage') as wrinkle on wrinkle.wrinkleid=USRSkinAnalysis.id INNER JOIN SYSChannelMembers on USRSkinAnalysis.syschannelmembers_id=SYSChannelMembers.id where account='$identifier' OR id_card='$identifier' OR phone_number='$identifier' AND syschannelmembers_id='$channelid'";
	
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
		echo '{"response":"PDO Exception","errcode":"b2ber003-287"}';
	}

}

?>