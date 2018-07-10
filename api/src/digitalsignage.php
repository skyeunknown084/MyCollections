<?php

function getdigitalsignlist($channelid){
 
	$selectQuery = "SELECT USRDigitalSignagesPages.id,USRDigitalSignagesPages.type,USRDigitalSignagesPages.name,USRDigitalSignagesPages.start_date,USRDigitalSignagesPages.end_date,USRDigitalSignagesPages.sort,USRDigitalSignages.size,USRDigitalSignages.url FROM USRDigitalSignagesPages INNER JOIN USRDigitalSignages ON USRDigitalSignagesPages.id = USRDigitalSignages.usrdigitalsignagespages_id WHERE USRDigitalSignagesPages.syschannelprofiles_id = '$channelid' AND USRDigitalSignagesPages.status = '1' ORDER BY USRDigitalSignagesPages.sort";
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

function createnewdigitalSign($body,$channelid){
	$dbLocal = getdbhimirrorqa();
	global $dbnamelocal,$ProductPhotoFilepath;
	
	$is_default=0;
	$lastIDProduct = "";
	$response="success";
	try{
		$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRDigitalSignagesPages'");
		$stmtgetProduct->execute();	
		$result= $stmtgetProduct->fetchAll();
	}catch(PDOException $e){
		$response="failed";
		echo '{"response":"PDO Exception","errcode":"b2ber003-32"}';
	}	
		$keylist="";
		$valuelist="";
		$typeval="";
		$namevalue="";
	if(count($result)!=0){
		foreach ($body as $key => $value){
			if($key=="name"||$key=="type"||$key=="sort"){
				if($key=="name")
					$typeval="'$value'";
				else if($key=="sort")
					$namevalue="'$value'";
			}else{
				$val=getInsertQueryaDigitalSign($key,$value,$result);
				if(count($val)!=0){
					if($val['key']=="is_default"&&$val['value']=="1"){
						$is_default=1;
					}
					if(empty($keylist)){
						$keylist.=$val['key'];
						$valuelist.=$val['value'];
					}else{
						
						$keylist.=",".$val['key'];
						$valuelist.=",".$val['value'];
					}
				}
			}
			
		}			
					$sortval1=1;
					$selectsort = "SELECT sort FROM `USRDigitalSignagesPages` WHERE syschannelprofiles_id = '$channelid' ORDER BY sort DESC LIMIT 1";    
				    try {
				        $stmtsel = $dbLocal->prepare($selectsort);
				        $stmtsel->execute();
				        $res = $stmtsel->fetchAll();
				        foreach ($res as $value) {
				            foreach ($value as $key => $val) {
				              
				                $sortval1 = $val + 1;
				                
				            }
				        }
				    } catch (PDOException $e) {
				        echo '{"response":"PDO Exception","errcode":"b2ber003-77"}';
				        $response='{"response":"PDO Exception","errcode":"b2ber003-77"}';
				    }
		$insertDigitalPage="";
		if($is_default){
			
			try{
				$stmtInsert = $dbLocal->prepare("Update USRDigitalSignagesPages set is_default=0 where syschannelprofiles_id='$channelid'");
				$stmtInsert->execute();
				$insertDigitalPage="insert into USRDigitalSignagesPages ($keylist,syschannelprofiles_id,createtime,type,sort,name) VALUES ($valuelist,'$channelid',CURRENT_TIMESTAMP,$typeval,$sortval1,'$sortval1')";
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-88"}';
				$insertDigitalPage="";
				$response='{"response":"PDO Exception","errcode":"b2ber003-88"}';
			}
			
		}else{
			$insertDigitalPage="insert into USRDigitalSignagesPages ($keylist,syschannelprofiles_id,createtime,type,sort,name) VALUES ($valuelist,'$channelid',CURRENT_TIMESTAMP,$typeval,$sortval1,'$sortval1')";
		
		}
		if(!empty($insertDigitalPage)){
			try{
				$stmtInsert = $dbLocal->prepare($insertDigitalPage);
				
				$stmtInsert->execute();
				$stmtLastInsertID = $dbLocal->prepare("SELECT id from USRDigitalSignagesPages ORDER by createtime DESC LIMIT 1");
				$stmtLastInsertID->execute();
				$getLastIDresult = $stmtLastInsertID->fetchAll();
				$lastIDProduct = $getLastIDresult[0]['id'];
			}catch(PDOException $e){
				echo '{"response":"PDO Exception","errcode":"b2ber003-107"}';
				$response='{"response":"PDO Exception","errcode":"b2ber003-107"}';
			}

			if(!empty($lastIDProduct)){
				try{
					$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRDigitalSignages'");
					$stmtgetProduct->execute();	
					$result2= $stmtgetProduct->fetchAll();
				}catch(PDOException $e){
					echo '{"response":"PDO Exception","errcode":"b2ber003-117"}';
					$response='{"response":"PDO Exception","errcode":"b2ber003-117"}';
				}
				if(count($result2)!=0){
					$countvalue=0;
					$keylist1="";
					$valuelist1="";
					$keylist2="";
					$valuelist2="";
					foreach ($body as $key => $value){
						if($key!="sort"){
							$val2=getInsertQueryaDigitalSign($key,$value,$result2);
						if(count($val2)!=0){
							for($i=0;$i<count($val2['value']);$i++){
								if($i==0){
									if(empty($keylist1)){
										$keylist1.=$val2['key'];
										if($val2['key']=="size")
											$valuelist1.=$val2['value'][$i];
										else
											$valuelist1.="'".$val2['value'][$i]."'";
									}else{
										
										$keylist1.=",".$val2['key'];
										if($val2['key']=="size")
											$valuelist1.=",".$val2['value'][$i];
										else
											$valuelist1.=","."'".$val2['value'][$i]."'";
									}
								}else if($i==1){
									if(empty($keylist2)){
										$keylist2.=$val2['key'];
										if($val2['key']=="size")
											$valuelist2.=$val2['value'][$i];
										else
											$valuelist2.="'".$val2['value'][$i]."'";
									}else{
										
										$keylist2.=",".$val2['key'];
										if($val2['key']=="size")
											$valuelist2.=",".$val2['value'][$i];
										else
											$valuelist2.=","."'".$val2['value'][$i]."'";
									}
								}
							}
						}
						}
						
					}
					$sortval=1;
					$selectsort = "SELECT sort FROM `USRDigitalSignages` WHERE syschannelprofiles_id = '$channelid' ORDER BY sort DESC LIMIT 1";    
				    try {
				        $stmtsel = $dbLocal->prepare($selectsort);
				        $stmtsel->execute();
				        $res = $stmtsel->fetchAll();
				        foreach ($res as $value) {
				            foreach ($value as $key => $val) {
				              
				                $sortval = $val + 1;
				                
				            }
				        }
				    } catch (PDOException $e) {
				        echo '{"response":"PDO Exception","errcode":"b2ber003-181"}';
				        $response='{"response":"PDO Exception","errcode":"b2ber003-181"}';
				    }
					if(!empty($keylist1)){
						$insertDigital="insert into USRDigitalSignages ($keylist1,syschannelprofiles_id,usrdigitalsignagespages_id,createtime,sort) VALUES ($valuelist1,'$channelid','$lastIDProduct',CURRENT_TIMESTAMP,$sortval)";
						
						try {
					        $smtdigi = $dbLocal->prepare($insertDigital);
					        $smtdigi->execute();
				        
					    } catch (PDOException $e) {
					        echo '{"response":"PDO Exception","errcode":"b2ber003-192"}';
					        $response='{"response":"PDO Exception","errcode":"b2ber003-192"}';
					    }
						
					}
					if(!empty($keylist2)){
						$insertDigital2="insert into USRDigitalSignages ($keylist2,syschannelprofiles_id,usrdigitalsignagespages_id,createtime,sort) VALUES ($valuelist2,'$channelid','$lastIDProduct',CURRENT_TIMESTAMP,$sortval)";
						try {
					        $smtdigi = $dbLocal->prepare($insertDigital2);
					        $smtdigi->execute();
				        
					    } catch (PDOException $e) {
					        echo '{"response":"PDO Exception","errcode":"b2ber003-204"}';
					        $response='{"response":"PDO Exception","errcode":"b2ber003-204"}';
					    }
					}
					echo $response;
				}else{
					echo '{"response":"Table Name Not Found","errcode":"b2ber006"}';
				}
			}else{
				echo '{"response":"Failed to get Last Inserted ID","errcode":"b2ber007"}';
			}
		}
	}else{
		echo '{"response":"Table Name Not Found","errcode":"b2ber006"}';
	}	
		
		
		
		
		
		
}
function deleteDigitalSignage($id){
	$dbLocal = getdbhimirrorqa();
	try{
		$stmtInsert = $dbLocal->prepare("Update USRDigitalSignagesPages set status=0 where id='$id'");
		$stmtInsert->execute();
		$stmtInsert2 = $dbLocal->prepare("Update USRDigitalSignages set status=0 where usrdigitalsignagespages_id='$id'");
		$stmtInsert2->execute();
		echo "successfully deleted";
	}catch (PDOException $e) {
		echo '{"response":"PDO Exception","errcode":"b2ber003-235"}';
					        
	}
	
}
function EditDigitalSignage($body,$channelid,$id){
	$dbLocal = getdbhimirrorqa();
	global $dbnamelocal,$ProductPhotoFilepath;
	try{
		$stmtget = $dbLocal->prepare("Select * FROM USRDigitalSignagesPages where id='$id'");
		$stmtget->execute();	
		$exist= $stmtget->fetchAll();
	}catch(PDOException $e){
		$response='{"response":"PDO Exception","errcode":"b2ber003-249"}';
		echo '{"response":"PDO Exception","errcode":"b2ber003-249"}';
	}

	if(count($exist)!=0){

		$is_default=0;
		$lastIDProduct = "";
		$response="success";
		try{
			$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRDigitalSignagesPages'");
			$stmtgetProduct->execute();	
			$result= $stmtgetProduct->fetchAll();
		}catch(PDOException $e){
			$response='{"response":"PDO Exception","errcode":"b2ber003-263"}';
			echo '{"response":"PDO Exception","errcode":"b2ber003-263"}';	
		}	
			$updatelist="";
			
		if(count($result)!=0){
			foreach ($body as $key => $value){
				$val=getInsertQueryaDigitalSign($key,$value,$result);
				if(count($val)!=0){
					if($val['key']!="name"&&$val['key']!="sort"&&$val['key']!="type"){
						if($val['key']=="is_default"&&$val['value']=="1"){
							$is_default=1;
						}
						if(empty($updatelist))
							$updatelist.=$val['key']."=".$val['value']."";	
						else
							$updatelist.=",".$val['key']."=".$val['value']."";	
					}
					
				}
				
			}
			$updateDigitalPage="";
			if($is_default){
			
				try{
					$stmtInsert = $dbLocal->prepare("Update USRDigitalSignagesPages set is_default=0 where syschannelprofiles_id='$channelid'");
					$stmtInsert->execute();
					$updateDigitalPage="Update USRDigitalSignagesPages set ".$updatelist." where id='$id'";
				}catch(PDOException $e){
					echo '{"response":"PDO Exception","errcode":"b2ber003-292"}';
					$updateDigitalPage="";
					$response='{"response":"PDO Exception","errcode":"b2ber003-292"}';
				}
				
			}else{
				$updateDigitalPage="Update USRDigitalSignagesPages set ".$updatelist." where id='$id'";
			
			}
			if(!empty($updateDigitalPage)){
				try{
					$stmtupdate = $dbLocal->prepare($updateDigitalPage);

					$stmtupdate->execute();
					
				}catch(PDOException $e){
					echo '{"response":"PDO Exception","errcode":"b2ber003-308"}';
					$response='{"response":"PDO Exception","errcode":"b2ber003-308"}';
				}
				try{
					$stmtgetProduct = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '".$dbnamelocal."' AND TABLE_NAME = 'USRDigitalSignages'");
					$stmtgetProduct->execute();	
					$result2= $stmtgetProduct->fetchAll();
				}catch(PDOException $e){
					echo '{"response":"PDO Exception","errcode":"b2ber003-316"}';
					$response='{"response":"PDO Exception","errcode":"b2ber003-316"}';
				}
				if(count($result2)!=0){
					$countvalue=0;
					$updatelist1="";
					$updatelist2="";
					$type1="";
					$type2="";
					foreach ($body as $key => $value){
						if($key!="sort"){
							$val2=getInsertQueryaDigitalSign($key,$value,$result2);
						if(count($val2)!=0){
							for($i=0;$i<count($val2['value']);$i++){
								if($val2['key']=="type"){
									if($i==0){
										$type1=$val2['value'][$i];
									}else{
										$type2=$val2['value'][$i];
									}
								}else{
									if($i==0){
										if(empty($updatelist1)){
											if($val2['key']=="size")
												$updatelist1.=$val2['key']."=".$val2['value'][$i]."";
											else
												$updatelist1.=$val2['key']."='".$val2['value'][$i]."'";	
										}
										else{
											if($val2['key']=="size")
												$updatelist1.=",".$val2['key']."=".$val2['value'][$i]."";
											else
												$updatelist1.=",".$val2['key']."='".$val2['value'][$i]."'";	
										}	
									}else{
										if(empty($updatelist2)){
											if($val2['key']=="size")
												$updatelist2.=$val2['key']."=".$val2['value'][$i]."";
											else
												$updatelist2.=$val2['key']."='".$val2['value'][$i]."'";	
										}
										else{
											if($val2['key']=="size")
												$updatelist2.=",".$val2['key']."=".$val2['value'][$i]."";
											else
												$updatelist2.=",".$val2['key']."='".$val2['value'][$i]."'";	
										}
									}
								}
								
							}
						}
						}
						
					}
					
					if(!empty($updatelist1)){
						$UpdateDigital="Update USRDigitalSignages set ".$updatelist1." where usrdigitalsignagespages_id='$id' AND type='$type1'";
						
						try {
					        $smtdigi = $dbLocal->prepare($UpdateDigital);
					        $smtdigi->execute();
				        
					    } catch (PDOException $e) {
					        echo '{"response":"PDO Exception","errcode":"b2ber003-380"}';
					        $response='{"response":"PDO Exception","errcode":"b2ber003-380"}';
					    }
					}
					if(!empty($updatelist2)){
						$UpdateDigital="Update USRDigitalSignages set ".$updatelist2." where usrdigitalsignagespages_id='$id' AND type='$type2'";
						
						try {
					        $smtdigi = $dbLocal->prepare($UpdateDigital);
					        $smtdigi->execute();
				        
					    } catch (PDOException $e) {
					        echo '{"response":"PDO Exception","errcode":"b2ber003-392"}';
					        $response='{"response":"PDO Exception","errcode":"b2ber003-392"}';
					    }
					    
					}
					echo $response;
				}else{
					echo '{"response":"Table Name Not Found","errcode":"b2ber006"}';
				}
			}else{
				echo '{"response":"Failed to get Last Inserted ID","errcode":"b2ber007"}';
			}
		}else{
			echo '{"response":"Table Name Not Found","errcode":"b2ber006"}';
		}
	}
	

}

function changeorderdigitalsignage($body,$channelid){
    $dbLocal     = getdbhimirrorqa();
    $idarray = array();
    $sortval = array();
    foreach ($body as $key => $value) {
       foreach ($value as $adkey => $advalue) {
            if($adkey=="id"){
                array_push($idarray, $advalue);
            }else{
                array_push($sortval, $advalue);
           }
       }
    }
    $count = 0;
    $counter = count($idarray);
    for ($i=0; $i < count($idarray); $i++) {
        $updateOrder = "UPDATE USRDigitalSignagesPages SET sort = $sortval[$i], name = '$sortval[$i]', updatetime = CURRENT_TIMESTAMP where id = '$idarray[$i]' AND syschannelprofiles_id = '$channelid'";
        try {
        $stmt = $dbLocal->prepare($updateOrder);
        $stmt->execute();
        $count++;
        } catch (PDOException $e) {
            echo '{"response":"PDO Exception","errcode":"b2ber003-366"}';
        }
    }

    if($count == $counter){
        echo "success";
    }else{
        echo "377 FAILED";
    }
}

function getInsertQueryaDigitalSign($key, $value, $columns)
{
    $returnarray = array();
    foreach ($columns as $columnskey => $columnsvalue) {
        
        if ($columnsvalue['COLUMN_NAME'] == $key) {
            $returnarray['key'] = $key;            
            if (strpos($columnsvalue['DATA_TYPE'], "int") !== false) {
                if(!empty($value)){
                    $returnarray['value'] = $value;
                }else{
                    $returnarray['value'] = 0;
                }                
            } else if (strpos($columnsvalue['DATA_TYPE'], "float") !== false) {
                $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "double") !== false) {
                $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "var") !== false) {
                if (!is_array($value)){
                	if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                }else
                    $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "char") !== false) {
                if (!is_array($value)){
                	if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                }else
                    $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "text") !== false) {
                if (!is_array($value)){
                	if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                }else
                    $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "date") !== false) {
                if (strpos($columnsvalue['COLUMN_NAME'], "create") !== false || strpos($columnsvalue['COLUMN_NAME'], "  ") !== false) {
                    $returnarray['value'] = "CURRENT_TIMESTAMP";
                }else {                    
                    $returnarray['value'] = "'" . $value . "'";
                }                
            } else if (strpos($columnsvalue['DATA_TYPE'], "time") !== false) {
                if (strpos($columnsvalue['COLUMN_NAME'], "create") !== false || strpos($columnsvalue['COLUMN_NAME'], "update") !== false) {
                    $returnarray['value'] = "CURRENT_TIMESTAMP";
                }else {                    
                    $returnarray['value'] = "'" . $value . "'";
                }
            }            
        }
    }
    return $returnarray;
}




?>