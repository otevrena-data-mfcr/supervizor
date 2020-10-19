<?php

interface IFakturyMapping{

  /*
   * Získá čas poslední aktualizace dat v katalogu
   */ 
  public function getTimestamp();
  
  /*
   * Funkce kterou se nastaví zdroj dat
   * jako parametr přijímá řetězec identifikující datovou sadu k importu (nejčastěji endpoint API katalogu)
   */
  public function setSource($source);
  
  /*
   * Funkce kterou se spustí import faktur
   * jako parametr přijímá objekt FakturyImport, do kterého zapisuje faktury pomocí funkcí insertRow() a insertRows()
   */   
  public function import(FakturyImport $fi);
  
}

?>