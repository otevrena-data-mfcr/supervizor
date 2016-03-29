<?php 

define("DB_HOST","127.0.0.1"); // TADY NASTAVTE HOSTNAME DATABÁZE
define("DB_USER","USERNAME"); // TADY NASTAVTE UŽIVATELSKÉ JMÉNO K DATABÁZI
define("DB_PASS","PASSWORD"); // TADY NASTAVTE HESLO K DATABÁZI
define("IMPORT_PASSWORD_MD5", "YOUR PASSWORD'S MD5 HASH"); // TADY NASTAVTE HESLO KE SPUŠTĚNÍ IMPORTU

// INTERNAL PATHS
define("WEB_DIR",realpath(__DIR__."/.."));
define("INCLUDE_PATH",WEB_DIR."/include");
define("STATIC_DIR",WEB_DIR."/static");	

define("APP_DIR",WEB_DIR."/app");
define("CONFIG_DIR",WEB_DIR."/config");

define("API_DIR",APP_DIR."/api");
define("LIB_DIR",APP_DIR."/lib");	
define("TMP_DIR",APP_DIR."/tmp");

// PUBLIC PATHS
define("WEB_ROOT","/supervizor");
define("API_ROOT",WEB_ROOT."/api");
define("STATIC_ROOT",WEB_ROOT."/static");

define("WEB_ROOT_FULL","http://".$_SERVER["SERVER_NAME"].WEB_ROOT);

// SETTINGS
define("TITLE","Supervizor Ministerstva financí");

/* is request ajax? */
define('IS_AJAX', (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') || @$_GET["is_ajax"]);

?>
