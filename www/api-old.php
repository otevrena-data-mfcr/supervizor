<?php

require "app/startup.php";

$db = loader("db");
$pdo = $GLOBALS["pdo"];

$endpoint = @$_GET["endpoint"];

ob_start("ob_gzhandler");

$last_check = $db->etl()->max("timestamp_dt");
$last_check_timestamp = strtotime($db->etl()->max("timestamp_dt")) ?: 0;

$tsstring = gmdate('D, d M Y H:i:s ', max(filemtime(__FILE__),$last_check_timestamp)) . 'GMT';

$etag = max(filemtime(__FILE__),$last_etl_timestamp);

$if_modified_since = isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
$if_none_match = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? $_SERVER['HTTP_IF_NONE_MATCH'] : false;

if ((($if_none_match && $if_none_match == $etag) || (!$if_none_match)) && ($if_modified_since && strtotime($if_modified_since) == strtotime($tsstring))){
    header('HTTP/1.1 304 Not Modified');
    exit;
}

header("Last-Modified: $tsstring");
header("ETag: {$etag}");

$cache_id = md5(join("%%%",array($_SERVER["REQUEST_URI"],$tsstring)));
$cache_path = TMP_DIR."/cache/$endpoint-$cache_id";

if(file_exists($cache_path)){
	header("Content-type: application/json; charset=utf-8");
	readfile($cache_path);
	exit;
}

$response = array(
	"success" => true,
	"error" => null,
	"result" => null,
);

$success = &$response["success"];
$error = &$response["error"];
$result = &$response["result"];

try{

	switch($endpoint){
		
		/* FAKTURY */
		case "faktury":
		
		$result = array("faktury" => array(),"stats" => array(),"dodavatele" => array());
		
		$faktury = $db->faktura();
		$faktury->limit(@$_GET["limit"] ?: 10);
		
		if(@$_GET["dodavatel"]) $faktury->where("dodavatel_id",$_GET["dodavatel"]);
		if(@$_GET["polozka"]) $faktury->where("faktura_polozka:polozka_id",(array) $_GET["polozka"]);
		elseif(@$_GET["skupina"]) $faktury->where("faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);
		
		
		
		//statistiky
		$stats = clone $faktury;
		$stats->select("COUNT(DISTINCT faktura.id) pocet,MIN(faktura_polozka:castka_am) min_castka_am,MAX(faktura_polozka:castka_am) max_castka_am,SUM(faktura.castka_am) objem, MAX(vystaveno_dt) AS max_vystaveno_dt, MIN(vystaveno_dt) AS min_vystaveno_dt");
		$result["stats"] = iterator_to_array($stats->fetch());
		
				
		if(@$_GET["polozka"] || @$_GET["skupina"])	$faktury->select("faktura.*,SUM(faktura_polozka:castka_am) as detail_castka_am")->group("faktura.id");
		else $faktury->select("faktura.*,castka_am as detail_castka_am");
		
		$faktury->order("detail_castka_am DESC,prijato_dt ASC");
		
		$faktury_id = array();
		foreach($faktury as $faktura){
			$row = iterator_to_array($faktura);
			unset($row["dodavatel_id"]);

			$row["dodavatel"] = iterator_to_array($faktura->dodavatel);
			
			$faktury_id[] = $row["id"];
			$dodavatele[] = $row["dodavatel"];
			
			$result["faktury"][] = $row;
			
		}
		
		//detaily polozky
		$faktury_polozky = array();
		$polozky = $db->faktura_polozka()->select("faktura_id,faktura_polozka.polozka_id,castka_am,polozka.nazev_st")->where("faktura_id",$faktury_id);
		if(@$_GET["polozka"]) $polozky->select("(CASE WHEN faktura_polozka.polozka_id IN (".join(",",array_map(array($pdo,"quote"),(array) $_GET["polozka"])).") THEN 1 ELSE 0 END) AS ve_vyberu");
		elseif(@$_GET["skupina"]) $polozky->select("(CASE WHEN polozka.skupina_polozka:skupina_id IN (".join(",",array_map(array($pdo,"quote"),(array) $_GET["skupina"])).") THEN 1 ELSE 0 END) AS ve_vyberu");
		
		foreach($polozky as $polozka) $faktury_polozky[$polozka["faktura_id"]][] = array("id" => $polozka["polozka_id"],"nazev_st" => $polozka["nazev_st"],"castka_am" => $polozka["castka_am"],"ve_vyberu" => $polozka["ve_vyberu"]);
		
		foreach($result["faktury"] as &$faktura){
			$faktura["polozky"] = $faktury_polozky[$faktura["id"]] ?: array();
		}
		
		
		
		//var_dump($faktury_id);exit;
		
		break;
		
		
		case "dodavatele":
		
		$result = array("data" => array(),"stats" => array());
		
		/* dodavatele */
		$dodavatele = $db->dodavatel();
		$dodavatele->limit(@$_GET["limit"] ?: 10);
		
		if(@$_GET["dodavatel"]) $dodavatele->where("dodavatel.id",$_GET["dodavatel"]);
		if(@$_GET["polozka"]) $dodavatele->where("faktura:faktura_polozka:polozka_id",(array) $_GET["polozka"]);
		elseif(@$_GET["skupina"]) $dodavatele->where("faktura:faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);
		
		$dodavatele->select("dodavatel.*,SUM(faktura:faktura_polozka:castka_am) as castka_celkem_am,COUNT(1) as pocet_celkem_no");
		
		$dodavatele->group("dodavatel.id")->order("castka_celkem_am DESC");
		
		foreach($dodavatele as $dodavatel){
			$i = count($result["data"]);
			$result["data"][$i] = iterator_to_array($dodavatel);
			$result["data"][$i]["faktury"] = array();
			$dodavatele_id[$dodavatel["id"]] = &$result["data"][$i];
		}
		
		/* faktury */
		$faktury = $db->faktura()->where("faktura.dodavatel_id",array_keys($dodavatele_id));
		
		if(@$_GET["dodavatel"]) $faktury->where("dodavatel_id",$_GET["dodavatel"]);
		if(@$_GET["polozka"]) $faktury->where("faktura_polozka:polozka_id",(array) $_GET["polozka"]);
		elseif(@$_GET["skupina"]) $faktury->where("faktura_polozka:polozka.skupina_polozka:skupina_id",$_GET["skupina"]);
		
		$faktury->select("faktura.*,SUM(faktura_polozka:castka_am) as detail_castka_am");
		$faktury->group("faktura.id");	
		$faktury->order("vystaveno_dt ASC");
		
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
		
		break;
		
		
		/* POLOZKY */
		
		case "polozky":
		$polozky_query = $db->faktura_polozka()->select("polozka.id,polozka.nazev_st AS nazev,SUM(faktura_polozka.castka_am) as objem, COUNT(1) as pocet");
		$polozky_query->group("polozka.id,polozka.nazev_st");

		$result = array();
		foreach($polozky_query as $row) $result[$row["id"]] = iterator_to_array($row);
		
		break;
		
		
		
		
		
		
		/* SKUPINY */
		case "skupiny":
		
		$result = array();
		
		$skupiny = $db->skupina_polozka()->select("skupina_id, SUM(polozka.faktura_polozka:castka_am) as objem, COUNT(1) as pocet")->group("skupina_id");
		if(@$_GET["skupina"]) $skupiny->where("skupina_id",@$_GET["skupina"]);
		foreach($skupiny as $skupina){
			$result[$skupina["skupina_id"]] = iterator_to_array($skupina);
			$result[$skupina["skupina_id"]]["polozky"] = array();
			
		}
		
		$polozky = $db->polozka()->select("polozka.id,polozka.nazev_st AS nazev,SUM(faktura_polozka:castka_am) as objem, COUNT(1) as pocet,skupina_polozka:skupina_id")->group("polozka.id");
		if(@$_GET["skupina"]) $polozky->where("skupina_polozka:skupina_id",@$_GET["skupina"]);
		foreach($polozky as $polozka){
			$result[$polozka["skupina_id"]]["polozky"][$polozka["id"]] = iterator_to_array($polozka);
		}
		
		break;
		
		case "skupinyFull":
		
			$result = array();
		
			$polozky = $db->polozka()->select("polozka.id,polozka.nazev_st AS nazev,SUM(faktura_polozka:castka_am) as objem");
			foreach($polozky as $polozka){
				$result[$polozka["polozka.id"]] = iterator_to_array($polozka);
			}
		
		break;
		
		
		
		
		
		
		
		/* OTHER */
		default:
		header("HTTP/1.1 400 Bad Request");
		exit;
		
	}

}catch(Exception $e){
	$response["success"] = false;
	$response["error"] = $e->getMessage();
	$response["result"] = null;
}

if(@$_GET["debug"]){
	header("Content-type: text/plain; charset=utf-8");
	var_dump(@$GLOBALS["queries"]);
	exit;
}

$output = json_encode($response,JSON_NUMERIC_CHECK);
file_put_contents($cache_path,$output);

header("Content-type: application/json; charset=utf-8");
echo $output;


?>