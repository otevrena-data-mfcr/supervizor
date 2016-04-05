<?php

function floatval_cs($string){
  return floatval(str_replace(",",".",str_replace(" ","",$string)));
}

class FakturyMapping_MMR implements IFakturyMapping{

  public $endpoint;
  
  public $metadata = array();
  
  public $rows_per_query = 3000;
  
  public function getTimestamp(){
    
    if(!@$this->metadata["last_modified"]) return false;

    $months = array(
    "Leden" => 1,
    "Únor" => 2,
    "Březen" => 3,
    "Duben" => 4,
    "Květen" => 5,
    "Červen" => 6,
    "Červenec" => 7,
    "Srpen" => 8,
    "Září" => 9,
    "Říjen" => 10,
    "Listopad" => 11,
    "Prosinec" => 12
    );
    
    $match = preg_match("/^[^\t]*\t(\d{1,2})\. (\w+) (\d{4}) \- (\d{1,2})\:(\d{1,2})$/",$this->metadata["last_modified"],$last_modified);
        
    if(!$match) return false;
    
    return mktime($last_modified[4],$last_modified[5],0,$months[$last_modified[2]],$last_modified[1],$last_modified[3]);
    
  }
  
  public function setSource($endpoint){
  
    $context = stream_context_create(array(
      'http' => array(
          'proxy' => PROXY,
          'request_fulluri' => true
      )
    ));

    $this->endpoint = $endpoint;
    $metadata = json_decode(file_get_contents($this->endpoint,false,$context),true);
    $this->metadata = $metadata["result"];
  }
  
  public function import(FakturyImport $fi){

    $resource_url = $this->metadata["url"];
    if(!$resource_url) throw new Exception("Chybí URL datového souboru.");
    
    $context = stream_context_create(array(
      'http' => array(
          'proxy' => PROXY,
          'request_fulluri' => true
      )
    ));
    
    if (($handle = fopen($resource_url, "r",false,$context)) !== FALSE){
    
    	//header
    	fgetcsv($handle, 0, ";");

    	//load_data
      $row = 0;
      do{
        
        $data = array();
      
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {			

          // convert to UTF-8
          array_walk($line,function(&$string){$string = iconv("WINDOWS-1250","UTF-8",$string);});          
          
          $data[] = $this->parseRow($line);
      		
          if(count($data) >= $this->rows_per_query) break;
      	}
        
        if(!count($data)) break;
        
        $row += count($data);
        //if($row > 100) break;
        
        $fi->insertRows($data);
        
      }while(1);

    	fclose($handle);
    }
  
  }

  public function parseRow($row){
  
  	$faktura_uid = array("MMR",$row[0],$row[1],$row[2]);
    
    $curr = array("KČ" => "CZK");   

    $dodavatel_id = $row[5] ?: substr(md5($row[4]),0,8);

    return array(
		  "faktura_id" => 		join("-",$faktura_uid),
		  "dodavatel_id" => 		$dodavatel_id,
		  "typ_dokladu_st" => 	"Faktura",
		  "rozliseni_st" => 		null,
		  "evidence_dph_in" => 	null,
		  "castka_am" => 			null,
		  "castka_bez_dph_am" =>	null,
		  "castka_orig_am" => 	null,
		  "uhrazeno_am" => 	   null,
		  "uhrazeno_orig_am" => 	null,
		  "mena_curr" => 			$row[7] ? (@$curr[$row[7]] ? $curr[$row[7]] : $row[7]) : null,
		  "vystaveno_dt" => 		null,
		  "prijato_dt" => 		$row[8] ? date("Y-m-d H:i:s",strtotime($row[8])) : null,
		  "splatnost_dt" => 		null,
		  "uhrazeno_dt" => 		$row[9] ? date("Y-m-d H:i:s",strtotime($row[9])) : null,
  		"ucel_tx" => 			$row[10] ?: null,
		  "dodavatel_ico_st" => 	$row[5] ? str_pad($row[5],8,"0",STR_PAD_LEFT) : null,
		  "dodavatel_nazev_st" => $row[4] ?: null,
  		"polozka_id" => 		((int) $row[11]) ?: null,
  		"polozka_castka_am" => 	$row[6] ? floatval_cs($row[6]) : null,
  		"polozka_nazev_st" => 	$row[12] ?: null
  	);
  }
  
}

?>