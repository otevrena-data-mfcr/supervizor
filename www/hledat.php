<?php
$page = (int) @$_GET["page"];
$s = (string) @$_GET["s"];
$type = (string) @$_GET["type"];

require "app/startup.php";

$db = loader("db");

$faktury = $db->faktura();

switch(@$_GET["type"]){
	
	case "dodavatel":
	$faktury->where("dodavatel.nazev_st LIKE ?","%".@$_GET["s"]."%");
	break;
	
	case "polozka":
	$faktury->where("faktura_polozka:polozka_id",@$_GET["s"]);
	break;
	
	default:
	header("HTTP/1.1 400 BAD REQUEST");
	exit;
	
}

if(IS_AJAX){
	$output = array("response" => array());
	foreach($faktury as $faktura){
		$output["response"][] = iterator_to_array($faktura);
	}
	
	$output["count"] = $faktury->count();
	header("Content-type: application/json; charset=utf-8");
	echo json_encode($output);
	exit;
}

$pocet_polozek = $faktury->count("*");
$pager = LethePager($faktury->count("*"),20,@$_GET["page"]);
$faktury->limit($pager["limit"],$pager["offset"]);
	
$faktury->order("dodavatel.nazev_st ASC,vystaveno_dt ASC");

$tpl = new LetheTemplate("layout.php");

$tpl->fetchOutput("body");
?>
<div id="hledani">
	<span class="loader"><span></span></span>
	<div class="filtr">
		<h3>Filtr</h3>
		<form>
			<div class="datum">
				<input type="date" name="datum_od" class="min">
				<input type="date" name="datum_do" class="max">
			</div>
			<div class="castka">
				<input type="number" class="min">
				<input type="number" class="max">
			</div>
		</form>

		<div class="status">
			<p><span class="pocet"><?=$pocet_polozek?></span> nalezených položek.</p>
		</div>
	</div>

	<div class="vysledky">
		<h3>Výsledky hledání: </h3>
		<table>
			<thead>
				<tr>
					<th>Dodavatel</th>
					<th>Datum vystavení</th>
					<th>Částka</th>
			</thead>

			<tbody>
				<?php foreach($faktury as $faktura): ?>
				<tr>
					<td><?=$faktura->dodavatel["nazev_st"]?></td>
					<td><?=$faktura["vystaveno_dt"]?></td>
					<td><?=$faktura["castka_am"]?></td>
				</tr>
				<?php endforeach;?>
			</tbody>

		</table>
		<div class="pager">
			Strana: 
			<a href="?page=1">1</a>

			<?php if($pager["current"] - 1 > 3): ?>
			<a>&hellip;</a>
			<?php endif; ?>

			<?php for($i = 2; $i < $pager["pages"]; $i++): ?>
			<?php if(abs($i - $pager["current"]) < 3): ?>
			<a href="?page=<?=$i?>"<?php if($i === $pager["current"]) echo " class=\"current\"";?>><?=$i?></a>
			<?php endif; ?>
			<?php endfor; ?>

			<?php if($pager["pages"] - $pager["current"] > 3): ?>
			<a>&hellip;</a>
			<?php endif; ?>

			<?php if($pager["pages"] !== 1): ?>
			<a href="?page=<?=$pager["pages"]?>"><?=$pager["pages"]?></a>
			<?php endif; ?>

			<?php if($pager["next"]): ?>
			<a href="?page=<?=$pager["next"]?>">&gt;</a><?php endif; ?>
		</div>

	</div>


</div>

<?php $tpl->display(); ?>