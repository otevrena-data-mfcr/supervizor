<?php

function db_connect($host,$user,$pass){
  if(!class_exists("NotORM")) require APP_DIR."/lib/notorm/NotORM.php";
  
  $driver_config = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"); 
  $pdo = new PDO("mysql:dbname=".PROFILE_DB.";host=$host",$user,$pass,$driver_config);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
  $cache = null;
  $structure = null;
  
  $db = new NotORM($pdo,$structure,$cache);
  
  function db_debug($query){global $queries;$queries[] = $query;}
  $db->debug = "db_debug";
  
  unset($driver_config);
  
  $GLOBALS["pdo"] = $pdo;
  
  return $db;
}

?>