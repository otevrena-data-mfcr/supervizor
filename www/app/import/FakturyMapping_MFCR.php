<?php

function floatval_cs($string){
  return floatval(str_replace(",",".",str_replace(" ","",$string)));
}

class FakturyMapping_MFCR implements IFakturyMapping{

  public $endpoint;
  
  public $metadata = array();
  
  public $rows_per_query = 3000;
  
  public function getTimestamp(){
    return @$this->metadata["last_modified"] ? strtotime($this->metadata["last_modified"]) : false;
  }
  
  public function setSource($endpoint){
    $this->endpoint = $endpoint;
    $metadata = json_decode(file_get_contents($this->endpoint),true);
    $this->metadata = $metadata["result"];
  }
  
  public function import(FakturyImport $fi){

    $resource_url = $this->metadata["url"];
    if(!$resource_url) throw new Exception("Chybí URL datového souboru.");
    
    
    if (($handle = fopen($resource_url, "r")) !== FALSE){
    
    	//header
    	fgetcsv($handle, 0, ";");

    	//load_data
      $row = 0;
      do{
        
        $data = array();
      
        while (($line = fgetcsv($handle, 0, ";")) !== FALSE) {			

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
  
  	$faktura_uid = array("MF");
  	
    switch($row[0]){
      case "Přijaté faktury": $faktura_uid[1] = "PF"; break;
      case "Ostatní platby": $faktura_uid[1] = "OP"; break;
      default: $faktura_uid[1] = "XX"; break;
    }
    
    $faktura_uid[2] = $row[1];
    

    return array(
  		"faktura_id" => 		join("-",$faktura_uid),
  		"dodavatel_id" => 		$row[3] ?: null,
  		"typ_dokladu_st" => 	$row[6] ?: null,
  		"rozliseni_st" => 		$row[0] ?: null,
  		"evidence_dph_in" => 	$row[7] ? (int) str_replace(array("Ano","Ne"),array("1","0"),$row[7]) : null,
  		"castka_am" => 			$row[9] ? floatval_cs($row[9]) : null,
  		"castka_bez_dph_am" =>	$row[10] ? floatval_cs($row[10]) : null,
  		"castka_orig_am" => 	$row[11] ? floatval_cs($row[11]) : null,
  		"uhrazeno_am" => 		$row[19] ? floatval_cs($row[19]) : null,
  		"uhrazeno_orig_am" => 	$row[20] ? floatval_cs($row[20]) : null,
  		"mena_curr" => 			$row[13] ?: null,
  		"vystaveno_dt" => 		$row[14] ? date("Y-m-d H:i:s",strtotime($row[14])) : null,
  		"prijato_dt" => 		$row[15] ? date("Y-m-d H:i:s",strtotime($row[15])) : null,
  		"splatnost_dt" => 		$row[16] ? date("Y-m-d H:i:s",strtotime($row[16])) : null,
  		"uhrazeno_dt" => 		$row[17] ? date("Y-m-d H:i:s",strtotime($row[17])) : null,
  		"ucel_tx" => 			$row[18] ?: null,
  		"dodavatel_ico_st" => 	$row[4] ? str_pad($row[4],8,"0",STR_PAD_LEFT) : null,
  		"dodavatel_nazev_st" => $row[2] ?: null,
  		"polozka_id" => 		((int) $row[21]) ?: null,
  		"polozka_castka_am" => 	$row[23] ? floatval_cs($row[23]) : null,
  		"polozka_nazev_st" => 	$row[22] ?: null
  	);
  }
  
}

?>