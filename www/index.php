<?php

$path = @$_GET["path"];

require "app/startup.php";

$tpl = new LetheTemplate("layout.php");

$tpl["body"] = new LetheTemplate("list.php");

$tpl->fetchOutput("scripts");
?>
<script src="<?=STATIC_ROOT?>/js/bubbles.js"></script>
<script src="<?=STATIC_ROOT?>/js/starsigns.js"></script>
<script src="<?=STATIC_ROOT?>/js/list.js"></script>
<script src="<?=STATIC_ROOT?>/js/index.js"></script>

<?php $tpl->fetchOutput("left"); ?>
<div id="bubliny"></div>
<div id="widget">
	<div class="back"><span class="glyphicon glyphicon-chevron-left"></span>ZPĚT</div>
	<div class="polozky template">
		<!--<table>
		<thead>
		<tr><th>Rozpočtová položka</th><th>Objem</th><th>Počet faktur</th></tr>
		</thead>
		<tbody></tbody>
		<tfoot>
		<tr><th>Celkem:</th><td class="objem"></td><td class="pocet"></td></tr>
		</tfoot>
		</table>-->
		<div class="polozka template">
			<h3 class="nazev"></h3>
			<p>
				<strong>Objem:</strong> <span class="objem"></span><br>
				<strong>Počet faktur:</strong> <span class="pocet"></span>
			</p>
		</div>
	</div>
</div>
<?php $tpl->display(); ?>