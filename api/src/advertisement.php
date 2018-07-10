<?php

function getadvertisementlist($channelid)
{
    
    $selectQuery = "SELECT * FROM USRAdvertisements WHERE USRAdvertisements.syschannelprofiles_id = '$channelid' AND status = '1' ORDER BY sort";
    try {
        $dbLocal      = getdbhimirrorqa();        
        $stmtget      = $dbLocal->prepare($selectQuery);
        $stmtget->execute();
        $result = $stmtget->fetchAll();
        echo json_encode($result);
    }
    catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-15"}'; 
    }
    
}

function createnewadvertisement($body, $channelid)
{
    $dbLocal           = getdbhimirrorqa();
    global $dbnamelocal,$ProductPhotoFilepath;
    $channel_name      = "";
    $selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id = '$channelid'";
    $stmt              = $dbLocal->prepare($selectchannelname);
    try {
        $stmt->execute();
        $res = $stmt->fetchAll();
        foreach ($res as $key => $value) {
            $channel_name = $value['name'];
        }        
    }
    catch (PDOException $e) {
       echo '{"response":"PDO Exception","errcode":"b2ber003-35"}'; 
    }
    try {
        $dbLocal     = getdbhimirrorqa();
        $stmtget     = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '" . $dbnamelocal . "' AND TABLE_NAME = 'USRAdvertisements'");
        $stmtget->execute();
        $result  = $stmtget->fetchAll();
        $keylist    = "";
        $valuelist  = "";
        $photolist  = "";
        foreach ($body as $key => $value) {
            
            $val = getInsertQueryadvertisement($key, $value, $result);
            if (count($val) != 0) {                
                if (empty($keylist)) {
                    if($val['key']=="is_default")
                        if($val['value'] == 1){
                            $updatedefault = "UPDATE USRAdvertisements SET is_default = 0 WHERE syschannelprofiles_id = '$channelid'";
                            $stmtupdate = $dbLocal->prepare($updatedefault);
                            $stmtupdate->execute();
                        }else{

                        }
                    if(strpos($val['value'],'https:')){
                        $keylist .= $val['key'];
                        $valuelist .= $val['value'];
                    }else{
                        $photoupload = checkdirectory($val['value']);
                        if($photoupload){
                            $pathval = str_replace("'", "", $val['value']);
                            $rand        = rand(0, 999);
                            $time        = time();
                            $extension   = explode(".", $pathval);
                            $extension   = strtolower(end($extension));
                            $hashvalue   = hash('sha256', php_uname('n') . $time . $rand);
                            $s3path      = "$channel_name/Advertisement/" . $hashvalue . "." . $extension;
                            $filename    = basename($pathval);
                            $filepath    = $ProductPhotoFilepath.$filename;
                            if(file_exists($filepath)){
                                $outputphoto = uploadtoS3($pathval, $s3path);                      
                                $keylist .= $val['key'];
                                $valuelist .= "'".$outputphoto."'";
                            }else{
                                $keylist .= $val['key'];
                                $valuelist .= "'N/A'";
                            }                            
                        }else{
                            $keylist .= $val['key'];
                            $valuelist .= $val['value'];                            
                        }                   
                    }
                    
                } else {
                    if($val['key']=="is_default")
                        if($val['value'] == 1){
                            $updatedefault = "UPDATE USRAdvertisements SET is_default = 0 WHERE syschannelprofiles_id = '$channelid'";
                            $stmtupdate = $dbLocal->prepare($updatedefault);
                            $stmtupdate->execute();
                        }else{
                            
                        }                 

                    if(strpos($val['value'],'https:')){
                        $keylist .= ",".$val['key'];
                        $valuelist .= ",".$val['value'];
                    }else{
                        $photoupload = checkdirectory($val['value']);
                        if($photoupload){
                            $pathval = str_replace("'", "", $val['value']);
                            $rand        = rand(0, 999);
                            $time        = time();
                            $extension   = explode(".", $pathval);
                            $extension   = strtolower(end($extension));
                            $hashvalue   = hash('sha256', php_uname('n') . $time . $rand);
                            $s3path      = "$channel_name/Advertisement/" . $hashvalue . "." . $extension;
                            $filename    = basename($pathval);
                            $filepath    = $ProductPhotoFilepath.$filename;
                            if(file_exists($filepath)){
                                $outputphoto = uploadtoS3($pathval, $s3path);                      
                                $keylist .= ",".$val['key'];
                                $valuelist .= ",'".$outputphoto."'";
                            }else{
                                $keylist .= ",".$val['key'];
                                $valuelist .= ",'N/A'";
                            }                            
                        }else{
                            $keylist .= ",".$val['key'];
                            $valuelist .= ",".$val['value'];
                            
                        }                   
                    }
                }
            }        
        }
    $selectsort = "SELECT sort FROM `USRAdvertisements` WHERE syschannelprofiles_id = '$channelid' ORDER BY sort DESC LIMIT 1";    
    try {
        $stmtsel = $dbLocal->prepare($selectsort);
        $stmtsel->execute();
        $res = $stmtsel->fetchAll();
        foreach ($res as $value) {
            foreach ($value as $key => $val) {
                $keylist.=",".$key;
                $sortval = $val + 1;
                $valuelist.=",".$sortval;
            }
        }
    } catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-142"}';
    }    
    $insertQuery="INSERT INTO USRAdvertisements ($keylist,syschannelprofiles_id) VALUES ($valuelist,'$channelid')";
    $stmt = $dbLocal->prepare($insertQuery);
        try {
            if($stmt->execute()){
                echo "success";
            }else{
                echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
            }

        } catch (PDOException $e) {
            echo '{"response":"PDO Exception","errcode":"b2ber003-154"}';
        }
    }
    catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-158"}';
    }    
}

function editadvertisement($adverId , $body, $channelid)
{
    $dbLocal           = getdbhimirrorqa();
    global $dbnamelocal,$ProductPhotoFilepath;
    $channel_name      = "";
    $selectchannelname = "SELECT name FROM SYSChannelProfiles WHERE id = '$channelid'";
    $stmt              = $dbLocal->prepare($selectchannelname);
    try {
        $stmt->execute();
        $res = $stmt->fetchAll();
        foreach ($res as $key => $value) {
            $channel_name = $value['name'];
        }        
    }
    catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-177"}'; 
    }
    try {
        $dbLocal     = getdbhimirrorqa();
        $stmtget     = $dbLocal->prepare("SELECT COLUMN_NAME, DATA_TYPE  FROM INFORMATION_SCHEMA.COLUMNS  WHERE TABLE_SCHEMA = '" . $dbnamelocal . "' AND TABLE_NAME = 'USRAdvertisements'");
        $stmtget->execute();
        $result  = $stmtget->fetchAll();
        $keylist    = "";
        $valuelist  = "";
        $photolist  = "";
        foreach ($body as $key => $value) {
            
            $val = getInsertQueryadvertisement($key, $value, $result);
            if (count($val) != 0) {
                if (empty($keylist)) {
                    if($val['value'] == 1){
                            $updatedefault = "UPDATE USRAdvertisements SET is_default = 0 WHERE syschannelprofiles_id = '$channelid'";
                            $stmtupdate = $dbLocal->prepare($updatedefault);
                            $stmtupdate->execute();
                        }else{
                            
                        }
                    if(strpos($val['value'],'https:')){
                        $keylist .= $val['key']."=".$val['value'];
                        
                    }else{
                        $photoupload = checkdirectory($val['value']);
                        if($photoupload){
                            $pathval = str_replace("'", "", $val['value']);
                            $rand        = rand(0, 999);
                            $time        = time();
                            $extension   = explode(".", $pathval);
                            $extension   = strtolower(end($extension));
                            $hashvalue   = hash('sha256', php_uname('n') . $time . $rand);
                            $s3path      = "$channel_name/Advertisement/" . $hashvalue . "." . $extension;
                            $filename    = basename($pathval);
                            $filepath    = $ProductPhotoFilepath.$filename;
                            
                            if(file_exists($filepath)){
                                $outputphoto = uploadtoS3($pathval, $s3path);                      
                                $keylist .= $val['key']."="."'".$outputphoto."'";
                            }else{
                                $keylist .= $val['key']."="."'N/A'";
                            }
                            
                        }else{
                            $keylist .= $val['key']."=".$val['value'];
                        }                   
                    }
                    
                } else {
                    if($val['value'] == 1){
                            $updatedefault = "UPDATE USRAdvertisements SET is_default = 0 WHERE syschannelprofiles_id = '$channelid'";
                            $stmtupdate = $dbLocal->prepare($updatedefault);
                            $stmtupdate->execute();
                        }else{
                            
                        }

                    if(strpos($val['value'],'https:')){
                        $keylist .= $val['key'];
                        $valuelist .= $val['value'];
                    }else{
                        $photoupload = checkdirectory($val['value']);
                        if($photoupload){
                            $pathval = str_replace("'", "", $val['value']);
                            $rand        = rand(0, 999);
                            $time        = time();
                            $extension   = explode(".", $pathval);
                            $extension   = strtolower(end($extension));
                            $hashvalue   = hash('sha256', php_uname('n') . $time . $rand);
                            $s3path      = "$channel_name/Advertisement/" . $hashvalue . "." . $extension;
                            $filename    = basename($pathval);
                            $filepath    = $ProductPhotoFilepath.$filename;                            
                            if(file_exists($filepath)){
                                $outputphoto = uploadtoS3($pathval, $s3path);                      
                                $keylist .= ",".$val['key']."="."'".$outputphoto."'";
                            }else{
                                $keylist .= ",".$val['key']."="."'N/A'";
                            }
                            
                        }else{
                            $keylist .= ",".$val['key']."=".$val['value'];
                        }                   
                    }
                }
            }          
        }
    $updateQuery="UPDATE USRAdvertisements SET $keylist, updatetime = CURRENT_TIMESTAMP WHERE id = '$adverId' AND syschannelprofiles_id = '$channelid'";
    $stmt = $dbLocal->prepare($updateQuery);
        try {
            if($stmt->execute()){
                echo "success";
            }else{
                echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
            }

        } catch (PDOException $e) {
             echo '{"response":"PDO Exception","errcode":"b2ber003-275"}';
        }
    }
    catch (PDOException $e) {
         echo '{"response":"PDO Exception","errcode":"b2ber003-279"}';
    }    
}

function advertisementgetdetails($adverid, $channelid)
{
    $dbLocal     = getdbhimirrorqa();
    $selectQuery = "SELECT * FROM USRAdvertisements WHERE id = '$adverid' AND syschannelprofiles_id = '$channelid'";
    try {
        $stmt = $dbLocal->prepare($selectQuery);
        if ($stmt->execute()) {
            $res = $stmt->fetchAll();
            echo json_encode($res);
        }else{
            echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
        }
    }
    catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-297"}';
    }
    
}

function checkdirectory($array){
    $pathextension = pathinfo($array);
    if(!empty($pathextension['extension'])){
        return true;
    }
}

function deleteadvertisementlist($ad_id,$channelid){
    $dbLocal     = getdbhimirrorqa();
    $deleteQuery = "UPDATE USRAdvertisements SET status = '0', updatetime = CURRENT_TIMESTAMP WHERE syschannelprofiles_id = '$channelid' AND id = '$ad_id'";
    try {
        $stmtdel = $dbLocal->prepare($deleteQuery);
        if($stmtdel->execute()){
            echo "success";
        }else{
            echo '{"response":"SMT Execute error","errcode":"b2ber005"}';
        }
    } catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-320"}';
    }
}

function getTotalspace($channelid){
    $dbLocal     = getdbhimirrorqa();
    $totalsize = "";
    $selectTotal = "SELECT available_space FROM `SYSChannelProfiles` WHERE id = '$channelid'";
    try {
        $stmt = $dbLocal->prepare($selectTotal);
        $stmt->execute();
        $res = $stmt->fetchAll();
              
        foreach ($res as $key => $value) {
           foreach ($value as $val) {
               $totalsize = $val;
           }
        }
    } catch (PDOException $e) {
        echo '{"response":"PDO Exception","errcode":"b2ber003-339"}';
    }
    echo $totalsize;
}

function changeorderadvertisement($body,$channelid){
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
        $updateOrder = "UPDATE USRAdvertisements SET sort = $sortval[$i] where id = '$idarray[$i]' AND syschannelprofiles_id = '$channelid'";
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

function getInsertQueryadvertisement($key, $value, $columns)
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
                if (!is_array($value))                    
                    if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                else
                    $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "char") !== false) {
                if (!is_array($value))
                    if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                else
                    $returnarray['value'] = $value;
            } else if (strpos($columnsvalue['DATA_TYPE'], "text") !== false) {
                if (!is_array($value))
                    if(!empty($value)){
                        $returnarray['value'] = "'" . str_replace("'", "''", $value) . "'";
                    }else{
                        $returnarray['value'] = "'N/A'";
                    }
                else
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