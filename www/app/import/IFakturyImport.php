<?php

interface IFakturyImport{
  
  /*
   * Funkce kterou se vloží jedna faktura do databáze
   * parametrem je pole, kde klíče jsou názvy položek databáze
   * array( pole1 => hodnota1, pole2 => hodnota2, ...)   
   */ 
  public function insertRow($row);
  
  /*
   * Funkce kterou se vloží více faktur do databáze
   * parametrem je pole polí, kde klíče jsou názvy položek databáze
   * array(0 => array( pole1 => hodnota1, pole2 => hodnota2, ...))   
   */ 
  public function insertRows($rows);
  
  /*
   * Získá čas poslední aktualizace dat v databázi
   */ 
  public function getTimestamp();
}

?>