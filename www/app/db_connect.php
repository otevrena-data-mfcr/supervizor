<?php

function db_connect($host,$user,$pass){
  if(!class_exists("NotORM")) require APP_DIR."/lib/notorm/NotORM.php";
  
  $driver_config = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
  $pdo = new PDO("mysql:dbname=".PROFILE_DB.";host=$host",$user,$pass,$driver_config);
  //$pdo = new PDO("mysql:dbname=zpevniky;host=127.0.0.1","root","",$driver_config);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  
  //$cache_file_name = str_replace("/","_",$_SERVER["SCRIPT_NAME"]);
  $cache = null;//new NotORM_Cache_Include(TMP_DIR."/notorm/notorm_cache_".$cache_file_name);
  
  $structure = null;//new NotORM_Structure_Discovery($pdo, $cache);
  
  $db = new NotORM($pdo,$structure,$cache);
  
  function db_debug($query){global $queries;$queries[] = $query;}
  $db->debug = "db_debug";
  
  unset($driver_config);
  
  $GLOBALS["pdo"] = $pdo;
  
  return $db;
}

?>