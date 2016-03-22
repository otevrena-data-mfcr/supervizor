<?php

require "app/startup.php";

$popup = (bool) @$_GET["popup"];

$tpl = new LetheTemplate($popup ? "layout-popup.php" : "layout.php");

$tpl["title"] = "O projektu";
$tpl->fetchOutput("styles");
?>

<style type="text/css">
	html,body{font-family:"Open Sans",Verdana,sans-serif;font-size:10pt;}
	
	h1{font-size:1.4em;margin:0;font-weight:bold;}
	h2{font-size: 1.2em;}
	h3{font-size:1.1em;margin:0 0 10px 0;font-weight:bold;}
	h4{margin:0;font-weight:bold;}
	table{border-collapse:collapse;}
	p{margin:10px 0;text-align:justify;}
  
  ul{margin-left:30px;}
  li{margin:5px 0;}
	
	.clear{display:block;clear:both;}
  
	<?php if(!$popup): ?>
	#menu_left{padding:20px;background-color:#fff;box-shadow: 0 0 5px rgba(0,0,0,.1);}

	<?php endif; ?>
	
	<?php if($popup): ?>
	html{height:100%;overflow:hidden;}
  body{height:100%;overflow:hidden;}
  #main{height:100%;overflow:hidden;}
	<?php endif; ?>

	
</style>
<?php $tpl->fetchOutput("body"); ?>
<h1>O projektu</h1>
<p>Projekt supervizor vizualizuje dataset Přehled faktur Ministerstva financí ČR, který je volně dostupný na adrese <a href="http://data.mfcr.cz/cs/dataset/prehled-faktur-ministerstva-financi-cr">http://data.mfcr.cz/cs/dataset/prehled-faktur-ministerstva-financi-cr</a>.</p>
 
<p>Cíl této aplikace je dvojí</p>
<ul>
  <li>
    Aktivně zpřístupnit hospodaření resortu ministerstva financí a v budoucnosti i jakýchkoliv dalších organizací, které projeví ochotu se do aplikace napojit. 
    <ul>
      <li>Pod pojmem „zveřejňování informací“ je potřeba vnímat vícero situací. Je možné informace například připíchnout na nástěnku v chodbě ministerstva nebo je poskytovat pouze na vyžádání. Je také možné publikovat informace jako scany v rámci internetových stránek v pododdíle oddílu odboru sekce XX, v lepším případě je možné na tomto místě publikovat strojově čitelná data, například v .xls formátu. Jako lepší možnost se jeví publikace dat v plně otevřeném formátu (http://goo.gl/iMikpt) v katalogu otevřených dat využitelná bez jakýchkoliv omezení. I tento způsob zveřejňování informací však znamená v mnohých případech omezení, protože je potřeba určité znalosti IT, aby se uživatel dostal ke konkrétním informacím. Cílem této aplikace je doplnit snahu projektu otevřených dat MF o zpřístupňování informací, tak abychom mohli říct, že jsme udělali maximum pro to, aby se tyto informace dostaly k občanům co nejblíže a v nejpřehlednější možné podobě. Podle kategorií lze dohledat jednotlivá data z faktur, kde je ihned vidět částka, dodavatel, předmět faktury a další informace. Pokud bude mít někdo zájem data hlouběji analyzovat, může si stáhnout zdrojový soubor z katalogu otevřených dat ministerstva financí. </li>
    </ul>
  </li>
  <li>Na praktickém a jednoduchém příkladu ukázat jeden z nejdůležitějších smyslů publikace dat v otevřeném formátu. Díky tomu, že tato data jsou již přístupná katalogu otevřených dat ministerstva financí, může podobnou aplikaci vyvinout absolutně kdokoliv. Instituce veřejné správy nemají v mnoha případech potřebné zdroje, zkušenosti a nadšení pro vývoj podobných aplikací a ani občas nemusí tušit, co přesně by bylo nejvhodnější vyvinout, ale nic nebrání tomu, aby jednoduše publikovaly data v otevřeném formátu a samotný vývoj nechali na veřejnosti.</li>
</ul>
<p>
Aplikace byla vyvinuta pracovníky ministerstva financí a vývoj trval celkem 10 člověkodnů. Aplikace není napojena na informační systém ministerstva financí, je napojena na <a href="http://data.mfcr.cz/">katalog otevřených dat ministerstva financí</a>, z kterého si data stahuje.</p>
<?php $tpl->display(); ?>