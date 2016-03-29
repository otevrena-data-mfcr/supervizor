<?php

require "app/startup.php";

$import = (bool) @$_POST["import"];
$password = (string) @$_POST["password"];

$db = loader("db");
$pdo = $GLOBALS["pdo"];

/* Nastav FakturyImport */
require_once APP_DIR."/import/FakturyImport.php";
$fi = new FakturyImport($pdo);

require_once APP_DIR."/import/FakturyMapping_".PROFILE_MAPPING.".php";
$mapping_class = "FakturyMapping_".PROFILE_MAPPING;
$mapping = new $mapping_class();
$mapping->setSource(PROFILE_ENDPOINT);


/* STAV */
$database_timestamp = $fi->getTimestamp();

$metadata_timestamp = $mapping->getTimestamp();

$needs_update = null;
if($metadata_timestamp && ($metadata_timestamp > $database_timestamp || !$database_timestamp)) $needs_update = true;
if($metadata_timestamp && ($metadata_timestamp === $database_timestamp)) $needs_update = false;

if($import){

  if(md5($password) !== IMPORT_PASSWORD_MD5) throw new Exception("Chybné heslo.");

  $start_time = microtime(true);
  
  $result = array();
  $result["imported_no"] = 0;
  $result["affected_no"] = 0;
  $result["updated_in"] = false;
  $result["forced_in"] = false;
  $result["endpoint_st"] = PROFILE_ENDPOINT;
  $result["last_modified_dt"] = $metadata_timestamp ? date("Y-m-d H:i:s",$metadata_timestamp) : null;
  $result["success_in"] = 1;
  
  try{

    $fi->import($mapping);
    
    $result["affected_no"] = $fi->affected;
    $result["imported_no"] = $fi->imported;
    $time_build = $fi->buildTime;
    
    $result["updated_in"] = true;

  }
  catch(Exception $e){
  	$result["success_in"] = 0;
  	$result["error_tx"] = $e->getMessage();
  }    
  
  $result["time_no"] = abs(microtime(true) - $start_time)*1000;
  
  $etl_item = array();
  
  try{
    $etl_item = $db->etl()->insert($result);
    $error = null;
  }catch(Exception $e){
  	$error = $e->getMessage();
  }
  
}

$tpl = new LetheTemplate("layout.php");
$tpl["full"] = true;
$tpl->fetchOutput("styles");
?>
<style type="text/css">

  table.import th{width:300px;}
  button{width:200px;}
</style>

<?php $tpl->fetchOutput("body");?>
<h2>Nastavení:</h2>
<table class="import">
  <tr>
    <th>Profil:</th>
    <td><?=PROFILE_ID ?: "N/A"?></td>
  </tr>
  <tr>
    <th>Dataset:</th>
    <td><?=PROFILE_DATASET ?: "N/A"?></td>
  </tr>
  <tr>
    <th>Databáze:</th>
    <td><?=PROFILE_DB ?: "N/A"?></td>
  </tr>
  <tr>
    <th>Endpoint:</th>
    <td><?=PROFILE_ENDPOINT ? "<a href=\"".PROFILE_ENDPOINT."\">".PROFILE_ENDPOINT."</a>" : "N/A"?></td>
  </tr>
  <tr>
    <th>Mapping profil:</th>
    <td><?=PROFILE_MAPPING?></a></td>
  </tr>
</table>

<h2>Aktuální stav:</h2>
<table class="import">
  <tr>
    <th>Poslední aktualizace databáze:</th>
    <td><?=$database_timestamp ? date("j. n. Y H:i:s",$database_timestamp) : "N/A"?></td>
  </tr>
  <tr>
    <th>Poslední aktualizace zdroje:</th>
    <td><?=$metadata_timestamp ? date("j. n. Y H:i:s",$metadata_timestamp) : "N/A"?></td>
  </tr>
  <tr>
    <th>Stav:</th>
    <td><?=$needs_update === true ? "Je dostupná aktualizace" : ($needs_update === false ? "Data jsou aktuální" : "Aktualizace není dostupná")?></td>
  </tr>
  <tr>
    <th>Počet faktur:</th>
    <td><?=$db->faktura()->count("*")?></td>
  </tr>
  <tr>
    <th>Počet dodavatelů:</th>
    <td><?=$db->dodavatel()->count("*")?></td>
  </tr>
  <tr>
    <th>Počet rozpočtových položek:</th>
    <td><?=$db->polozka()->count("*")?></td>
  </tr>
</table>

<h2>Výsledek importu:</h2>
<table class="import">
  <?php if($import): ?>
  
  <tr>
    <th>Datum a čas:</th>
    <td><?=date("j. n. Y H:i")?></td>
  </tr>
  <tr>
    <th>ID importu:</th>
    <td><?=$etl_item["id"]?></td>
  </tr>
  <tr>
    <th>Stav:</th>
    <td><?=$result["success_in"] ? "Úspěch" : "Nastala chyba: ".htmlspecialchars($result["error_tx"])?></td>
  </tr>
  
  <tr>
    <th>Provedena aktualizace dat:</th>
    <td><?=$result["updated_in"] ? "Ano" : "Ne"?><?=$result["forced_in"] ? " (vynuceně)" : ""?></td>
  </tr>
  
  <tr>
    <th>Změny:</th>
    <td>
      Importováno: <?=$result["imported_no"]?>,
      Upraveno v DB: <?=$result["affected_no"]?>,
      Smazáno dočasných souborů: <?=$fi->deletedFiles?>
    </td>
  </tr>
  
  <tr>
    <th>Doba importu:</th>
    <td>Celkem: <?=round(($result["time_no"])/1000,1)?> s (stažení a uložení dat: <?=round(($result["time_no"] - $time_build)/1000,2)?> s, zpracování dat: <?=round($time_build/1000,2)?> s)</td>
  </tr>
  
  <tr>
    <th>Využití RAM:</th>
    <td><?=round(memory_get_peak_usage(true)/1024/1024,2)?> MB</td>
  </tr>
  
  <tr>
    <th>Dávkování:</th>
    <td><?=$fi->queryCount?> žádostí, průměrně <?=round($result["imported_no"]/$fi->queryCount)?> faktur na žádost</td>
  </tr>
  
  <?php else: ?>
  <tr>
    <th>Stav:</th>
    <td>Nespuštěno</td>
  </tr>
  
  <?php endif; ?>
  
</table>
<br>
<form action="<?=$_SERVER["PHP_SELF"]?>" method="POST">
  <input type="hidden" name="import" value="1">
  <button type="submit">Spustit import</button>
  Heslo: <input type="password" name="password" required> 
</form>

<?php $tpl->display(); ?>
