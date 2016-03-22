<?php


require "app/startup.php";

$db = loader("db");
$pdo = $GLOBALS["pdo"];

$endpoint = @$_GET["endpoint"];

/* VALIDATE REQUEST */
$api_endpoints = array();
foreach(array_slice(scandir(API_DIR."/"),2) as $filename) $api_endpoints[] = pathinfo($filename,PATHINFO_FILENAME);

if(!in_array($endpoint,$api_endpoints)){
	header("HTTP/1.1 400 Bad Request");
	exit;
}

$api_executable = API_DIR."/".$endpoint.".php";

/* gzip */
ob_start("ob_gzhandler");

/* CLIENT CACHE */
$last_changes = array();
$last_changes[] = strtotime($db->etl()->where("updated_in",1)->max("timestamp_dt")) ?: 0;
$last_changes[] = filemtime(__FILE__);
$last_changes[] = filemtime($api_executable);
$last_changes[] = filemtime(WEB_DIR."/import.php");

$last_change = max($last_changes);

$tsstring = gmdate('D, d M Y H:i:s ', $last_change) . 'GMT';
$etag = $last_change."%".PROFILE_DB;

$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;

if ((($if_none_match && $if_none_match == $etag) || (!$if_none_match)) && ($if_modified_since && strtotime($if_modified_since) == strtotime($tsstring))){
    header('HTTP/1.1 304 Not Modified');
    exit;
}

//header("Last-Modified: $tsstring");
header("ETag: {$etag}");

/* SERVER CACHE */
$cache_id = md5(join("%%%",array($_SERVER["REQUEST_URI"],$tsstring,PROFILE_DB)));
$cache_path = TMP_DIR."/cache/$endpoint-$cache_id.cache";

if(file_exists($cache_path)){
	header("Content-type: application/json; charset=utf-8");
	readfile($cache_path);
	exit;
}

/* RESPONSE */

$response = array(
	"success" => true,
	"error" => null,
	"result" => null,
);

$success = &$response["success"];
$error = &$response["error"];
$result = &$response["result"];



try{	

	$result = require $api_executable;

}catch(Exception $e){
	$response["success"] = false;
	$response["error"] = $e->getMessage();
	$response["result"] = null;
}

if(@$_GET["debug"]){
	header("Content-type: text/plain; charset=utf-8");
	var_dump(@$GLOBALS["queries"]);
	exit;
}

$output = json_encode($response,JSON_NUMERIC_CHECK);
file_put_contents($cache_path,$output);

header("Content-type: application/json; charset=utf-8");
echo $output;


?>