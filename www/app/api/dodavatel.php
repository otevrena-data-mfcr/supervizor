<?php

$dodavatel_id = @$_GET["dodavatel"];

if(!$dodavatel_id){header("HTTP/1.1 400 BAD REQUEST");exit;}

$result = array("id" => $dodavatel_id,"db" => array(),"kamos" => array());

/* DB */
$db = loader("db");
$result["db"] = iterator_to_array($db->dodavatel[$dodavatel_id]);

/* KAMOS */
if($result["db"]["ico_st"]){
	$ico = str_pad($result["db"]["ico_st"],8,"0",STR_PAD_LEFT);

	$context = stream_context_create(array(
		'http' => array(
			'method' => 'GET',
			'header' => "X-MFCR-Hello: Cau Jirko! :)\r\n"
		)
	));

  $context = stream_context_create(array(
    'http' => array(
        'proxy' => PROXY,
        'request_fulluri' => true
    )
  ));
    
	$kamos_data = @file_get_contents("http://kamos.datlab.cz/company/CZ".$ico,false,$context);
		
	if(@$kamos_data){
		$result["kamos"] = json_decode($kamos_data,true);

		$entities_decode = array("company_name");
		foreach($entities_decode as $key){
			if(@$result["kamos"][$key]) $result["kamos"][$key] = htmlspecialchars_decode($result["kamos"][$key]);
		}
	}
	else $result["kamos"] = null;
}

return $result;
?>