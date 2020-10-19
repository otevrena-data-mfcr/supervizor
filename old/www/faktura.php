<?php

$faktura_id = (string) @$_GET["faktura"];
$popup = (bool) @$_GET["popup"];

require "app/startup.php";

$db = loader("db");

$faktura = $db->faktura[$faktura_id];

if(!$faktura) {
	header("HTTP/1.1 404 NOT FOUND");
	die("Not found.");
}

if(!$popup){
	$skupiny = $db->skupina()->where("skupina_polozka:polozka.faktura_polozka:faktura_id",$faktura["id"])->select("skupina.id,skupina.nazev_st,skupina.barva,SUM(skupina_polozka:polozka.faktura_polozka:castka_am) AS castka_sum")->group("skupina.id");
}

function nf_cs($number){
	return number_format($number, 2, ",", " ");
}

$typy_dokladu = array(
  "faktura" => "Faktura",
  "záloha" => "Zálohová faktura",
  "ostatní" => "Ostatní platba"
);
$typ = @$typy_dokladu[$faktura["typ_dokladu_st"]] ?: "Ostatní platba";

$share_url = WEB_ROOT_FULL."/faktura/$faktura[id]";

$tpl = new LetheTemplate($popup ? "layout-popup.php" : "layout.php");

$tpl["share_url"] = $share_url;
$tpl["thumbnail"] = WEB_ROOT_FULL."/static/img/invoice.png";
$tpl["description"] = "$faktura[typ_dokladu_st] č. $faktura[id] dodavatele ".htmlspecialchars($faktura->dodavatel["nazev_st"])." za ".nf_cs($faktura["uhrazeno_am"])." Kč. Účel faktury: ".htmlspecialchars($faktura["ucel_tx"]);

$tpl["title"] = "$faktura[typ_dokladu_st] č. $faktura[id]";
$tpl->fetchOutput("styles");
?>
<style type="text/css">
	html,body{font-family:"Open Sans",Verdana,sans-serif;font-size:10pt;}
	
	h1{font-size:1.4em;margin:0;font-weight:bold;}
	h2{font-size: 1.2em;}
	h3{font-size:1.1em;margin:0 0 10px 0;font-weight:bold;}
	h4{margin:0;font-weight:bold;}
	table{border-collapse:collapse;}
	p{margin:0;}
	
	.clear{display:block;clear:both;}
	
	#faktura{position:relative;max-width:600px;margin:0px auto;padding:38px 0 0 0;}
	
	#faktura .faktura{padding:40px 40px 80px 40px;overflow:auto;}
	
	#faktura .faktura > table{border-collapse:collapse;width:100%;}
	
	#faktura td,#faktura th{text-align:left;vertical-align:top;}
	#faktura tbody td{border:2px solid #000;padding:10px;}
	
	#faktura .share{position:absolute;top:0;left:0;width:100%;height:38px;background-color:rgb(51, 122, 183);color:#fff;}
	#faktura .share th{text-align:left;width:80px;}
	#faktura .share h2{display:inline;position:absolute;top:0;left:0;height:38px;padding:8px 10px;margin:0;}
	
	#faktura .share .input {margin-left:80px;padding:8px 10px;width:300px;}
	#faktura .share .input input{font-family:inherit;width:100%;height:22px;color:#000;padding:1px 5px;cursor:text;}
	
	#faktura .share .widgets{position:absolute;top:0;right:0;background-color:rgba(255,255,255,.2);height:38px;padding:9px 10px;}
	#faktura .share .widget{display:inline-block;height:20px;vertical-align:middle;}
	
	#dodavatel{width:50%;}
	#odberatel{width:50%;}
	
	#faktura table table th{border:none;padding:0 2px;}
	#faktura table table td{border:none;padding:0 2px;}
	
	#menu_left h3{margin-top:20px;}
	#list_skupiny{margin-left:25px;}
	
	table.rozpad{width:100%;}
	table.rozpad tbody td{border:none;padding:0;}
	table.rozpad thead tr{border-bottom:1px solid #000;}
	table.rozpad tbody tr{border-bottom:1px solid #999;}
		
	table.castky{width:100%;}
	table.castky tbody td{border:none;padding:0;}
	table.castky thead tr{border-bottom:1px solid #000;}
	table.castky tbody tr{border-bottom:1px solid #999;}
	table.castky tbody td{border-left:1px solid #eee;}
	
	.castka{text-align:right !important;}
	
	#faktura td.celkem h3{margin:0;}
	#faktura td.celkem .castka{float:right;}
	
	#faktura .disclaimer{margin-bottom:15px;text-align:center;color:#f00;font-weight:bold;}
  #faktura .disclaimer_op{margin:10px 0;font-weight:bold;}
	
	<?php if(!$popup): ?>
	#faktura{background-color:#fff;box-shadow: 0 0 5px rgba(0,0,0,.1);}
	#menu_left{padding:20px;background-color:#fff;box-shadow: 0 0 5px rgba(0,0,0,.1);}

	<?php endif; ?>
	
	<?php if($popup): ?>
	html{height:100%;overflow:hidden;}
  body{height:100%;overflow:hidden;}
  #main{height:100%;overflow:hidden;}
  #faktura{height:100%;box-sizing:border-box;max-width:none;}
	#faktura .faktura{padding:20px 30px;box-sizing:border-box;height:100%;max-height:822px;overflow:auto;}
	#faktura .share .input input{height:16px;}
	<?php endif; ?>
	
</style>

<?php $tpl->fetchOutput("scripts"); ?>
<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/cs_CZ/sdk.js#xfbml=1&version=v2.4";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>


<?php $tpl->fetchOutput("left"); ?>
<div id="menu_left">
	<a href="<?=WEB_ROOT?>/">&lArr;Návrat na úvodní stránku</a>
	
	<h3>Skupiny do kterých patří tato faktura:</h3>
	<ul id="list_skupiny">
		<?php foreach($skupiny as $skupina): ?>
		<li><a href="<?=WEB_ROOT?>/skupina/<?=$skupina["id"]?>"><?=htmlspecialchars($skupina["nazev_st"])?> (<?=nf_cs($skupina["castka_sum"])?> CZK)</a></li>
		<?php endforeach; ?>
	</ul>
</div>

<?php $tpl->fetchOutput("body"); ?>


<div id="faktura">
	<div class="share">
		<h2>Sdílet:</h2>
		<div class="input">
			<input type="text" value="<?=htmlspecialchars($share_url)?>" readonly id="input_share_link" onClick="this.select();">
		</div>
		<div class="widgets">
			<div class="widget fb-share-button" data-href="<?=$share_url?>" data-layout="button_count"></div>
			<div class="widget"><a href="https://twitter.com/share" class="twitter-share-button" data-via="otevrenadatamf">Tweet</a></div>
		</div>
	</div>
	<div class="faktura">
		
		<p class="disclaimer">TOTO JE VIZUALIZACE DAT, NEJEDNÁ SE O SKUTEČNOU PODOBU DOKLADU.</p>

		<table>
			<thead>
				<tr><th colspan="2"><h1><?=$faktura["typ_dokladu_st"]?> č. <?=end(explode("-",$faktura["id"]))?></h1></th></tr>
			</thead>
			<tbody>
				<?php if($faktura["typ_dokladu_st"] !== "Ostatní platba"): ?>
        <tr>
					<td id="dodavatel">
						<h3>Dodavatel</h3>
						<h4><?=htmlspecialchars($faktura->dodavatel["nazev_st"])?></h4>
						<p>
							IČO: <?=$faktura->dodavatel["ico_st"]?>
						</p>
					</td>

					<td id="odberatel">
						<h3>Odběratel</h3>
						<h4><?=htmlspecialchars(PROFILE_ENTITY)?></h4>
						<p><?=nl2br(htmlspecialchars(PROFILE_ENTITY_DESC))?></p>
					</td>
				</tr>
        <?php endif; ?>
				<tr>
					<td>
						<table class="datumy">
							<?php if($faktura["typ_dokladu_st"]): ?><tr><th>Typ dokladu:</th><td><?=$faktura["typ_dokladu_st"]?></td></tr><?php endif; ?>
							<?php if($faktura["rozliseni_st"]): ?><tr><th>Rozlišení:</th><td><?=$faktura["rozliseni_st"]?></td></tr><?php endif; ?>
							<?php if($faktura["evidence_dph_in"]): ?><tr><th>Evidence DPH:</th><td><?=$faktura["evidence_dph_in"] ? "ano" : "ne"?></td></tr><?php endif; ?>
						</table>
					</td>
					<td>
						<table class="datumy">
							<?php if($faktura["vystaveno_dt"]): ?><tr><th>Vystaveno:</th><td><?=date("j. n. Y",strtotime($faktura["vystaveno_dt"]))?></td></tr><?php endif; ?>
							<?php if($faktura["prijato_dt"]): ?><tr><th>Přijato:</th><td><?=date("j. n. Y",strtotime($faktura["prijato_dt"]))?></td></tr><?php endif; ?>
							<?php if($faktura["splatnost_dt"]): ?><tr><th>Splatnost:</th><td><?=date("j. n. Y",strtotime($faktura["splatnost_dt"]))?></td></tr><?php endif; ?>
							<?php if($faktura["uhrazeno_dt"]): ?><tr><th>Uhrazeno:</th><td><?=date("j. n. Y",strtotime($faktura["uhrazeno_dt"]))?></td></tr><?php endif; ?>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">

						<h3>Účel platby</h3>
						<p class="ucel"><?=htmlspecialchars($faktura["ucel_tx"])?></p>
            
            <?php if($faktura["typ_dokladu_st"] === "ostatní"): ?>
            <p class="disclaimer_op">Poznámka: V rámci ostatních plateb mohou být zahrnuty i náklady proplacené zaměstnancům ve spojení s výkonem práce, například cestovné.</p>
            <?php endif; ?>
				  </td>
		    </tr>
				<tr>
					<td colspan="2">
						<h3>Rozpis dle rozpočtových položek</h3>
						<table class="rozpad">
							<thead>
								<tr>
									<th colspan="2">Rozpočtová položka</th>
									<th class="castka">Částka v Kč s DPH</th>
								</tr>
							<tbody>
								<?php foreach($faktura->faktura_polozka() as $polozka): ?>
								<tr>
									<th><?=$polozka["polozka_id"]?></th>
									<th><?=$polozka->polozka["nazev_st"]?></th>
									<td class="castka"><?=nf_cs($polozka["castka_am"])?> CZK</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<h3>Detailní rozpis částek</h3>
						<table class="castky">
							<thead>
								<tr>
									<th></th>
									<th class="castka">Částka v původní měně</th>
									<th class="castka">Částka v Kč bez DPH</th>
									<th class="castka">Částka v Kč s DPH</th>
								</tr>
							</thead>
							<tbody>
								<tr>
                  <th>Vystavená částka</th>
                  <td class="castka"><?php if(is_numeric(@$faktura["castka_orig_am"])): ?><?=nf_cs($faktura["castka_orig_am"])?> <?=$faktura["mena_curr"]?><?php else: ?>N/A<?php endif; ?></td>
                  <td class="castka"><?php if(is_numeric(@$faktura["castka_bez_dph_am"])): ?><?=nf_cs($faktura["castka_bez_dph_am"])?> CZK<?php else: ?>N/A<?php endif; ?></td>
                  <td class="castka"><?php if(is_numeric(@$faktura["castka_am"])): ?><?=nf_cs($faktura["castka_am"])?> CZK<?php else: ?>N/A<?php endif; ?></td>
                </tr>
								<tr>
                  <th>Uhrazená částka</th>
                  <td class="castka"><?php if(is_numeric(@$faktura["uhrazeno_orig_am"])): ?><?=nf_cs($faktura["uhrazeno_orig_am"])?> <?=$faktura["mena_curr"]?><?php else: ?>N/A<?php endif; ?></td>
                  <td class="castka"><?php if(is_numeric(@$faktura["uhrazeno_bez_dph_am"])): ?><?=nf_cs($faktura["uhrazeno_bez_dph_am"])?> CZK<?php else: ?>N/A<?php endif; ?></td>
                  <td class="castka"><?php if(is_numeric(@$faktura["uhrazeno_am"])): ?><?=nf_cs($faktura["uhrazeno_am"])?> CZK<?php else: ?>N/A<?php endif; ?></td>
                </tr>
							</tbody>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="celkem">
						<h3>Uhrazeno celkem <span class="castka"><?=nf_cs($faktura["uhrazeno_am"])?> CZK</span></h3>
						<span class="clear"></span>
					</td>

				</tr>
			</tbody>
		</table>
	</div>
</div>

<?php $tpl->display(); ?>