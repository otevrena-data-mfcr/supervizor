<?php

require "app/startup.php";

$ico = @$_GET["ico"];

if(!$ico){
	header("HTTP/1.1 400 BAD REQUEST");
	exit;
}

$context = stream_context_create(array(
  'http' => array(
      'proxy' => PROXY,
      'request_fulluri' => true
  )
));
  
$hledani = file_get_contents("https://or.justice.cz/ias/ui/rejstrik-%24firma?jenPlatne=PLATNE&ico=".$ico,false,$context);

if(!$hledani){
	header("Content-type: text/plain; charset=utf-8");
	echo "Nastala chyba při hledání subjeku v rejstříku.";
	exit;
}
else{

	preg_match("/\.\/rejstrik\-firma\.vysledky\?subjektId=(\d+)\&amp;typ=PLATNY/",$hledani,$matches);

	$subjektId = @$matches[1];

	if(!$subjektId){
		header("Content-type: text/plain; charset=utf-8");
		echo "Osoba nemá záznam v obchodním rejstříku. Pravděpodobně se jedná o fyzickou osobu - podnikatele.";
		exit;
	}
}

$url = "https://or.justice.cz/ias/ui/rejstrik-firma.vysledky?typ=PLATNY&subjektId=".$subjektId;

header('Location: ' . $url, true, 301);
exit;

?>