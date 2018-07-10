<?php

include("globalLocal.php");

function getdblocaluser(){
	global $hostnamelocal,$dbnamelocal,$usernamelocal,$passwordlocal;
    $pdo = new PDO("mysql:host=" . $hostnamelocal . ";dbname=".$dbnamelocal ,
        $usernamelocal, $passwordlocal,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") ); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

function login($username, $password, $channel_name){

	$dbLocal = getdbhimirrorqa();

	try{
		if(!empty($password)&&!empty($username)){
			
			try{
				if(empty($channel_name)||$channel_name=="admin"){
					$stmtget = $dbLocal->prepare("SELECT account,createtime,is_admin,is_manager FROM `SYSChannelUsers` where account='$username' AND password='$password' AND status=1 AND is_admin=1");
				}else{
					$stmtget = $dbLocal->prepare("SELECT syschannelprofiles_id,name,logo_url,account,SYSChannelUsers.createtime,is_admin,is_manager FROM `SYSChannelUsers` LEFT JOIN SYSChannelProfiles on SYSChannelProfiles.id=SYSChannelUsers.syschannelprofiles_id where account='$username' AND password='$password' AND name='$channel_name' AND SYSChannelUsers.status=1 AND is_manager=1");
				}
				
				if($stmtget->execute()){
					$res = $stmtget->fetchAll();
					$numofdata = $stmtget->rowCount();
					if($numofdata > 0){
						echo json_encode($res);
					}else
						echo '{"response":"login failed","errorcode":"b2ber001a"}';
				}else
					echo '{"response":"failed","errorcode":"b2ber001b"}';
			}catch(PDOException $e){
				echo '{"response":"failed","errorcode":"b2ber001c"}'.$e;
			}
			
		}else{
			echo '{"response":"failed","errorcode":"b2ber001d"}';
		}
	}catch(PDOException $e){
		echo '{"response":"failed","errorcode":"b2ber101","message":"'.$e.'"}';
	}

}


?>