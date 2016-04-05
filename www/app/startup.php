<?php

/*
if($_SERVER["REMOTE_ADDR"] !== "90.180.72.190"){
	header("Content-type:text/plain;charset=utf-8");
	die("Na webu něco důležitého měním, opět spuštěn bude co nevidět.");
}
*/

/* regional settings */
date_default_timezone_set("Europe/Prague");
setlocale(LC_ALL,"cs_CZ.utf-8");
mb_internal_encoding("UTF-8");


/* SESSION */
session_start();


/* INCLUDES */
require_once __DIR__."/../config/constants.php";
require_once LIB_DIR."/Lethe/LetheTemplate.php";


/* include paths */
$include_paths = array();
$include_paths[] = INCLUDE_PATH;
set_include_path(join(PATH_SEPARATOR,$include_paths));


$profiles = json_decode(file_get_contents(CONFIG_DIR."/profiles.json"),true);

/* SET SOURCE DB */
if(@$_GET["profil"]){
  $_SESSION["profile"] = $_GET["profil"];
  $_SESSION["dataset"] = null;
}
if(@$_GET["dataset"]){
  $_SESSION["dataset"] = $_GET["dataset"];
}

/* DETECT SOURCE DB */
if(@$_SESSION["profile"] && @$profiles[$_SESSION["profile"]]){
  if(@$_SESSION["dataset"] && @$profiles[$_SESSION["profile"]]["datasets"][$_SESSION["dataset"]]){
    $dataset = $profiles[$_SESSION["profile"]]["datasets"][$_SESSION["dataset"]];
    $dataset_id = $_SESSION["dataset"];
  }
  else{
    $dataset = current($profiles[$_SESSION["profile"]]["datasets"]);
    $dataset_id = key($profiles[$_SESSION["profile"]]["datasets"]);
  }
  $profile = $profiles[$_SESSION["profile"]];
  $profile_id = $_SESSION["profile"];
  
}
elseif(@$profiles["mfcr"]["datasets"]){
  $profile = $profiles["mfcr"];
  $profile_id = "mfcr";
  $dataset = current($profiles["mfcr"]["datasets"]);
  $dataset_id = key($profiles["mfcr"]["datasets"]);
  
}
else{
  $profile = current($profiles);
  $profile_id = key($profiles);
  $dataset = current($profile["datasets"]);
  $dataset_id = key($profile["datasets"]); 
}

define("PROFILE_DB",@$dataset["database"]);
define("PROFILE_ENDPOINT",@$dataset["endpoint"]);
define("PROFILE_MAPPING",@$dataset["mapping"]);
define("PROFILE_ID",$profile_id);
define("PROFILE_DATASET",$dataset_id);
define("PROFILE_SOURCE_NAME",@$dataset["source_name"]);
define("PROFILE_SOURCE_URL",@$dataset["source_url"]);
define("PROFILE_ENTITY",@$profile["entity"]);
define("PROFILE_ENTITY_DESC",@$profile["entity_desc"]);

unset($profile,$profile_id,$dataset_id);

/* Lazy loading of resources */
$loader = array("_loaded" => array());
function loader($what){
  $loader = &$GLOBALS["loader"];
  
  if(!@$loader["_loaded"][$what]){ //slo by i jen zjistit, jestli je to funkce, ale co kdyz chceme nacist funkci?
    $loader[$what] = $loader[$what]();
    $loader["_loaded"][$what] = true;
  }
  
  return $loader[$what];
}

$loader["db"] = function(){
  require APP_DIR."/db_connect.php";
  return db_connect(DB_HOST,DB_USER,DB_PASS);
}; 

?>