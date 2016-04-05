<?php

require_once LIB_DIR."/Lethe/LethePager.php";

$page = (int) @$_GET["page"] ?: 1;
$per_page = @$_GET["limit"] ? max($_GET["limit"],100) : 10;
	
$result = array("dodavatele" => array(),"stats" => array(),"pager" => array());

/* dodavatele */
$dodavatele = $db->dodavatel();

if(@$_GET["dodavatel"]) $dodavatele->where("dodavatel.id",$_GET["dodavatel"]);
if(@$_GET["polozka"]) $dodavatele->where("faktura:faktura_polozka:polozka_id",(array) $_GET["polozka"]);
elseif(@$_GET["skupina"]) $dodavatele->where("faktura:faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);
if(@$_GET["datum"]["min"]) $dodavatele->where("faktura:vystaveno_dt >= ?",date("Y-m-d",is_numeric($_GET["datum"]["min"]) ? $_GET["datum"]["min"] : strtotime($_GET["datum"]["min"])));
if(@$_GET["datum"]["max"]) $dodavatele->where("faktura:vystaveno_dt <= ?",date("Y-m-d",is_numeric($_GET["datum"]["max"]) ? $_GET["datum"]["max"] : strtotime($_GET["datum"]["max"])));

$dodavatele->select("dodavatel.*,SUM(faktura:faktura_polozka:castka_am) as castka_celkem_am,COUNT(1) as pocet_celkem_no");
$dodavatele->group("dodavatel.id")->order("castka_celkem_am DESC");

/* pager */
$pager = LethePager($dodavatele->count("DISTINCT dodavatel.id"),$per_page,$page);
$result["pager"] = $pager;
					  
$dodavatele->limit($pager["limit"],$pager["offset"]);

$dodavatele_id = array();

foreach($dodavatele as $dodavatel){
	$i = count($result["dodavatele"]);
	$result["dodavatele"][$i] = iterator_to_array($dodavatel);
	$result["dodavatele"][$i]["faktury"] = array();
	$dodavatele_id[$dodavatel["id"]] = &$result["dodavatele"][$i];
	/*
	$ico = str_pad($dodavatel["ico_st"],8,"0",STR_PAD_LEFT);
	if(@$dodavatel["ico_st"]) $result["dodavatele"][$i]["kamos"] = @json_decode(file_get_contents("http://kamos.datlab.cz/company/CZ".$ico));
	*/
}

/* faktury */
$faktury = $db->faktura()->where("faktura.dodavatel_id",array_keys($dodavatele_id));

if(@$_GET["dodavatel"]) $faktury->where("dodavatel_id",$_GET["dodavatel"]);
if(@$_GET["polozka"]) $faktury->where("faktura_polozka:polozka_id",(array) $_GET["polozka"]);
elseif(@$_GET["skupina"]) $faktury->where("faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);
if(@$_GET["datum"]["min"]) $faktury->where("vystaveno_dt >= ?",date("Y-m-d",is_numeric($_GET["datum"]["min"]) ? $_GET["datum"]["min"] : strtotime($_GET["datum"]["min"])));
if(@$_GET["datum"]["max"]) $faktury->where("vystaveno_dt <= ?",date("Y-m-d",is_numeric($_GET["datum"]["max"]) ? $_GET["datum"]["max"] : strtotime($_GET["datum"]["max"])));

$faktury->select("faktura.*,UNIX_TIMESTAMP(faktura.uhrazeno_dt) as uhrazeno_udt, SUM(faktura_polozka:castka_am) as detail_castka_am");
$faktury->group("faktura.id");	
$faktury->order("vystaveno_dt ASC");

$faktury_id = array();

foreach($faktury as $faktura){
	$dodavatel = &$dodavatele_id[$faktura["dodavatel_id"]];
	$j = count($dodavatel["faktury"]);

	$dodavatel["faktury"][$j] = iterator_to_array($faktura);
	$dodavatel["faktury"][$j]["polozky"] = array();

	$faktury_id[$faktura["id"]] = &$dodavatel["faktury"][$j];
}

/* polozky */

$polozky = $db->faktura_polozka()->select("faktura_polozka.*,polozka.nazev_st")->where("faktura_id",array_keys($faktury_id));

if(@$_GET["polozka"]) $polozky->select("(CASE WHEN faktura_polozka.polozka_id IN (".join(",",array_map(array($pdo,"quote"),(array) $_GET["polozka"])).") THEN 1 ELSE 0 END) AS ve_vyberu");
elseif(@$_GET["skupina"]) $polozky->select("(CASE WHEN polozka.skupina_polozka:skupina_id IN (".join(",",array_map(array($pdo,"quote"),(array) $_GET["skupina"])).") THEN 1 ELSE 0 END) AS ve_vyberu");


foreach($polozky as $polozka){

	$faktura = &$faktury_id[$polozka["faktura_id"]];
	$faktura["polozky"][] = iterator_to_array($polozka);			

}

/* stats */

//statistiky
$stats = $db->faktura();
$stats->select("COUNT(DISTINCT faktura.id) pocet,MIN(faktura_polozka:castka_am) min_castka_am,MAX(faktura_polozka:castka_am) max_castka_am,SUM(faktura.castka_am) objem, MAX(vystaveno_dt) AS max_vystaveno_dt, MIN(vystaveno_dt) AS min_vystaveno_dt");

if(@$_GET["dodavatel"]) $stats->where("dodavatel.id",$_GET["dodavatel"]);

if(@$_GET["polozka"]) $stats->where("faktura_polozka:polozka_id",(array) $_GET["polozka"]);
elseif(@$_GET["skupina"]) $stats->where("faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);

$result["stats"] = iterator_to_array($stats->fetch());

return $result;

?>
