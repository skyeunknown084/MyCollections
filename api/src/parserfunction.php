<?php
// Routes


function getheadervalue($headers,$key){
	$keyvalue="HTTP_".strtoupper($key);
	$value="";
	foreach ($headers as $name => $values) {
		if($name==$keyvalue){
			$value=implode(", ", $values);
		}
    	
	}
	return $value;
}
?>