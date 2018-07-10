<?php
// Routes
include("globalLocal.php");

function getdblocal(){
	global $hostnamelocal,$dbnamelocal,$usernamelocal,$passwordlocal;
    $pdo = new PDO("mysql:host=" . $hostnamelocal . ";dbname=".$dbnamelocal ,
        $usernamelocal, $passwordlocal,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") ); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}
function getdbhimirrorqa(){
    global $hostnamelocal,$dbnamelocal,$usernamelocal,$passwordlocal;
    $pdo = new PDO("mysql:host=" . $hostnamelocal . ";dbname=".$dbnamelocal ,
        $usernamelocal, $passwordlocal,array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") ); 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
}

?>