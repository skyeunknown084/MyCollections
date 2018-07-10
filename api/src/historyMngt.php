<?php
include("globalLocal.php");

function getIp(){
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ipadd = $_SERVER['HTTP_CLIENT_IP'];
    }elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ipadd = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }else{
        $ipadd = $_SERVER['REMOTE_ADDR'];
    }
    return $ipadd;
}

function getProductMngtHistory($username, $page_name, $mode, $old_data, $new_data, $dbLocal){
    
    $create_time = date("Y-m-d h:i:s");
    $ipaddress = getIp();

    try{
        $insertQ = "INSERT into B2BHistoryManagement (`page_name`, `username`, `ip_address`, `mode`, `old_data`, `new_data`, `created_time`) VALUES ('".$page_name."','".$username."','".$ipaddress."','".$mode."','".$old_data."','".$new_data."','".$create_time."')";
        $stmtinsert = $dbLocal->prepare($insertQ);
        if($stmtinsert->execute()){
            //echo "success";
        }else{
            //echo "failed";
        }
    }catch(PDOException $e){
        echo $e->getMessage();
    }
}

function getNewData($id, $dbLocal){

    try{
        $getNewDataQ = "SELECT * from B2BSkinCareProduct WHERE prod_id = '".$id."'";
        $stmtNewData = $dbLocal->query($getNewDataQ);
        $newData = $stmtNewData->fetchAll();
        return json_encode($newData);
    }catch(PDOException $e){
        echo "Inserting new Data to History ERROR: ".$e->getMessage();
    }
}

?>
