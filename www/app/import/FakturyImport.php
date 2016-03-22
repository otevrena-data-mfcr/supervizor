<?php

require_once __DIR__."/IFakturyImport.php";
require_once __DIR__."/IFakturyMapping.php";

class FakturyImport implements IFakturyImport{

  public $pdo;
  public $insert_columns = array("faktura_id", "dodavatel_id", "typ_dokladu_st", "rozliseni_st", "evidence_dph_in", "castka_am", "castka_bez_dph_am", "castka_orig_am", "uhrazeno_am", "uhrazeno_orig_am", "mena_curr", "vystaveno_dt", "prijato_dt", "splatnost_dt", "uhrazeno_dt", "ucel_tx", "dodavatel_ico_st", "dodavatel_nazev_st", "polozka_id", "polozka_castka_am", "polozka_nazev_st");  
  
  public $affected = 0;
  public $imported = 0;
  public $buildTime = 0;
  public $queryCount = 0;
  public $deletedFiles = 0;
  
  private $importQueries = array();  
  

  public function __construct($pdo){
    $this->pdo = $pdo;
  }
  
  public function getTimestamp(){
    $database_max = $this->pdo->query("SELECT MAX(last_modified_dt) FROM etl WHERE updated_in=1;")->fetch();
    return @$database_max[0] ? strtotime($database_max[0]) : 0;
  }
  
  public function getImportQuery($count = 1){
  
    if(!@$this->importQueries[$count]){
      $fields = join(",",$this->insert_columns);
      $questionmarks = rtrim(str_repeat("(".rtrim(str_repeat("?,",count($this->insert_columns)),",")."),",$count),",");
      
      $insert_query = "INSERT INTO raw_load ($fields) VALUES $questionmarks";
      
      $this->importQueries[$count] = $this->pdo->prepare($insert_query);
    }
    
    return $this->importQueries[$count]; 
  }

  public function importClear(){
  
    $this->pdo->exec("DELETE FROM raw_load");
    //$db->raw_load()->delete();

  }
  
  public function insertRow($row){
    return $this->insertRows(array($row));
  }
  
  public function insertRows($rows){
    
    $count = count($rows);
    $data = array();
    while($row = array_shift($rows)){
      foreach($this->insert_columns as $col) array_push($data, @$row[$col]);
    }
    
    $this->getImportQuery($count)->execute($data);
    //$db->raw_load()->insert($data);
    
    $this->imported += $count;
    $this->queryCount++;
    
  }
  
  public function build($preserve = false){

    $pdo = $this->pdo;
  
    $pdo->exec("DELETE FROM faktura_polozka");
    //$db->faktura_polozka()->delete();
		
    $pdo->exec("DELETE FROM faktura");
		//$db->faktura()->delete();
		
    $pdo->exec("DELETE FROM dodavatel");
		//$db->dodavatel()->delete();
		
    $pdo->exec("DELETE FROM polozka");
		//$db->polozka()->delete();

		//create dodavatel
		$this->affected += $pdo->exec("INSERT INTO dodavatel (id,ico_st,nazev_st) (SELECT dodavatel_id,MAX(COALESCE(dodavatel_ico_st,'00000000')),MAX(dodavatel_nazev_st) FROM raw_load WHERE dodavatel_id IS NOT NULL GROUP BY dodavatel_id)");
		//create polozka
		$this->affected += $pdo->exec("INSERT INTO polozka (id,nazev_st) (SELECT COALESCE(polozka_id,0),MAX(COALESCE(polozka_nazev_st,'')) FROM raw_load GROUP BY polozka_id)");
		//create faktura
		$this->affected += $pdo->exec("INSERT INTO faktura (id,dodavatel_id,typ_dokladu_st,rozliseni_st,evidence_dph_in,castka_am,castka_bez_dph_am,castka_orig_am,uhrazeno_am,uhrazeno_orig_am,mena_curr,vystaveno_dt,prijato_dt,splatnost_dt,uhrazeno_dt,ucel_tx) (SELECT faktura_id, MAX(dodavatel_id), MAX(typ_dokladu_st), MAX(rozliseni_st), MAX(evidence_dph_in), MAX(castka_am), MAX(castka_bez_dph_am), MAX(castka_orig_am), MAX(uhrazeno_am), MAX(uhrazeno_orig_am), MAX(mena_curr), MAX(vystaveno_dt), MAX(prijato_dt), MAX(splatnost_dt), MAX(uhrazeno_dt), MAX(ucel_tx) FROM raw_load WHERE faktura_id IS NOT NULL GROUP BY faktura_id)");
		//create faktura_polozka
		$this->affected += $pdo->exec("INSERT INTO faktura_polozka (faktura_id,polozka_id,castka_am) (SELECT faktura_id,COALESCE(polozka_id,0),MAX(CASE WHEN polozka_id IS NOT NULL THEN polozka_castka_am ELSE castka_am END) FROM raw_load GROUP BY faktura_id,polozka_id HAVING COUNT(1) = 1)");
		
		$pdo->exec("UPDATE polozka SET nazev_st = 'Rozpočtová položka neurčena' WHERE id = 0;");
		//$pdo->exec("UPDATE polozka SET nazev_st = 'Rozpočtová položka neurčena' WHERE id = 0;");
		
		//$db->transaction = 'COMMIT';
		
    if(!$preserve) $pdo->exec("DELETE FROM raw_load");
		//if(!@$_GET["preserve"]) $db->raw_load()->delete();
  
  }
  
  public function import(IFakturyMapping $mapping){
  
    // prepare space for import
    $this->importClear();
    
    // import
    $mapping->import($this);    

    // convert 2D table to relational database
    $buildStart = microtime(true);
    $this->build();
    $this->builtTime = (microtime(true) - $buildStart)*1000;
    
    /* clear cache */
    $files_to_remove = glob(TMP_DIR."/cache/*.cache");
    while($file_to_remove = array_pop($files_to_remove)) if(unlink($file_to_remove)) $this->deletedFiles++;
  
  }
  
  
  
}

?>