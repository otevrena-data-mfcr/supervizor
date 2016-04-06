<?php
$result = array(
	"skupiny" => array(),
	"stats" => array()
);

/* SKUPINY */
$skupiny = $db->skupina()->select("skupina.id, skupina.nazev_st AS nazev, skupina.popis_tx AS popis, skupina.x,skupina.y,skupina.barva, SUM(COALESCE(skupina_polozka:polozka.faktura_polozka:castka_am,0)) as objem, COUNT(1) as pocet,MAX(UNIX_TIMESTAMP(skupina_polozka:polozka.faktura_polozka:faktura.uhrazeno_udt)) AS max_uhrazeno_udt,MIN(UNIX_TIMESTAMP(skupina_polozka:polozka.faktura_polozka:faktura.uhrazeno_dt)) AS min_uhrazeno_udt")->group("skupina_id");
if(@$_GET["skupina"]) $skupiny->where("skupina_id",@$_GET["skupina"]);
$skupiny->order("objem DESC");

$stats = array(
	"max" => null,
	"min" => null,
	"total" => 0
);

$skupiny_list = array();
foreach($skupiny as $skupina){
	$result["skupiny"][$skupina["id"]] = iterator_to_array($skupina);
	$result["skupiny"][$skupina["id"]]["polozky"] = array();
	
	$skupiny_list[] = $skupina["id"];
	
	$stats["max"] = max($stats["max"],$skupina["objem"]);
	$stats["min"] = $stats["min"] === null ? $skupina["objem"] : min($stats["min"],$skupina["objem"]);
	$stats["total"] += $skupina["objem"];
}

/* STATS */
$result["stats"] = $stats;

/* POLOZKY */
$polozky = $db->polozka()->select("polozka.id,polozka.nazev_st AS nazev,SUM(faktura_polozka:castka_am) as objem, COUNT(1) as pocet,skupina_polozka:skupina_id")->where("skupina_id",$skupiny_list)->group("polozka.id")->order("objem DESC");
foreach($polozky as $polozka){
	//$result["skupiny"][$polozka["skupina_id"]]["polozky"][$polozka["id"]] = iterator_to_array($polozka);
	$result["skupiny"][$polozka["skupina_id"]]["polozky"][] = iterator_to_array($polozka);
}

return $result;

?>