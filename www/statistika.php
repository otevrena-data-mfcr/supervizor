<?php

require "app/startup.php";

$db = loader("db");

$skupiny_pocet = array(array("Skupina","Počet faktur"));
$skupiny_objem = array(array("Skupina","Objem v mil. Kč"));
$pocet = $objem = 0;
foreach($db->skupina()->select("skupina.nazev_st AS nazev,COUNT(skupina_polozka:polozka.faktura_polozka:polozka_id) AS pocet")->group("skupina.id")->order("pocet DESC") as $skupina){
  $skupiny_pocet[] = array(
    0 => $skupina["nazev"],
    1 => (int) $skupina["pocet"]
   );
   $pocet += $skupina["pocet"];
}
foreach($db->skupina()->select("skupina.nazev_st AS nazev,SUM(skupina_polozka:polozka.faktura_polozka:castka_am) as objem")->group("skupina.id")->order("objem DESC") as $skupina){
   $skupiny_objem[] = array(
    0 => $skupina["nazev"],
    1 => max(round($skupina["objem"] / 1000000,1),0)
   );
   $objem += $skupina["objem"];
}
$skupiny_pocet[] = array(
  0 => "Bez rozpočtové položky",
  1 => $db->faktura_polozka()->count("faktura_id") - $pocet
);
$skupiny_objem[] = array(
  0 => "Bez rozpočtové položky",
  1 => round(($db->faktura_polozka()->sum("castka_am") - $objem) / 1000000,1)
);

$polozky_objem = array(array("Rozpočtová položka","Objem v mil. Kč"));
$objem = 0;
foreach($db->polozka()->select("polozka.id AS id,polozka.nazev_st AS nazev,SUM(faktura_polozka:castka_am) AS objem")->group("polozka.id")->order("objem DESC") as $polozka){
  if($polozka["id"] == 0) continue;
  $polozky_objem[] = array(
    0 => $polozka["id"]."-".$polozka["nazev"],
    1 => max(round($polozka["objem"] / 1000000,1),0)
   );
   $objem += $polozka["objem"];
}
$polozky_objem[] = array(
  0 => "Bez rozpočtové položky",
  1 => round(($db->faktura_polozka()->sum("castka_am") - $objem) / 1000000,1)
);


$tpl = new LetheTemplate("layout.php");
$tpl["full"] = true;
$tpl->fetchOutput("styles");
?>
<style type="text/css">
#skupiny{margin-bottom:20px;}
#skupiny .graf{float:left;margin-right:20px;}
</style>
<?php $tpl->fetchOutput("scripts");?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
  google.charts.load("current", {packages:["corechart"]});
  google.charts.setOnLoadCallback(drawCharts);

  // Callback that creates and populates a data table,
  // instantiates the pie chart, passes in the data and
  // draws it.
  function drawCharts() {

    // Create the data table.
    var dataSkupinyPocet = new google.visualization.arrayToDataTable(<?=json_encode($skupiny_pocet)?>);
    var dataSkupinyObjem = new google.visualization.arrayToDataTable(<?=json_encode($skupiny_objem)?>);
    var dataPolozkyObjem = new google.visualization.arrayToDataTable(<?=json_encode($polozky_objem)?>);

    // Set chart options
    var optionsSkupiny_pocet = {
      "title": "Počet faktur dle skupin",
      'width':550,
      'height':400,
      pieHole: 0.4,
      chartArea: {
        width:"80%",
        height:"80%"
      },
      legend: {alignment:"center"}
    };
    
    var optionsSkupiny_objem = {
      "title": "Objem v mil. Kč dle skupin",
      'width':550,
      'height':400,
      pieHole: 0.4,
      chartArea: {
        width:"80%",
        height:"80%"
      },
      legend: {alignment:"center"}
    };
    
    var optionsPolozky = {
      "title": "Objem v mil. Kč dle rozpočtových položek",
      'width':1120,
      'height':700,
      chartArea: {
        width:"90%",
        height:"90%"
      },
      pieHole: 0.4,
      legend: {
        position:"labeled"
      }
    };

    // Instantiate and draw our chart, passing in some options.
    var chartSkupinyPocet = new google.visualization.PieChart(document.getElementById('graf_skupiny_pocet'));
    chartSkupinyPocet.draw(dataSkupinyPocet, optionsSkupiny_pocet);
    
    var chartSkupinyObjem = new google.visualization.PieChart(document.getElementById('graf_skupiny_objem'));
    chartSkupinyObjem.draw(dataSkupinyObjem, optionsSkupiny_objem);
    
    var chartSkupinyObjem = new google.visualization.PieChart(document.getElementById('graf_polozky_objem'));
    chartSkupinyObjem.draw(dataPolozkyObjem, optionsPolozky);
  }
</script>

<?php $tpl->fetchOutput("body");?>
<div id="skupiny">
  <div class="graf" id="graf_skupiny_pocet"></div>
  <div class="graf" id="graf_skupiny_objem"></div>
  <span class="clear"></span>
</div>

<div class="graf" id="graf_polozky_objem"></div>

<?php $tpl->display(); ?>