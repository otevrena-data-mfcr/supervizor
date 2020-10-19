<?php

$db = loader("db");

$last_check = $db->etl()->max("last_modified_dt");

$posledni_aktualizace = $last_check ? date("j. n. Y",strtotime($last_check)) : false;
  
?>
<div id="footer">
  <div>
  <div id="soutez">
  <p><a href="http://www.otevrenadata.cz/soutez/rocnik-2015/"><img src="<?=STATIC_ROOT?>/img/fom.png"> Aplikace Supervizor získala 1. místo v soutěži Společně otevíráme data 2015.</a></p>
    
    </div>
  	<span class="copy">Zdroj dat: <a href="<?=PROFILE_SOURCE_URL?>"><?=PROFILE_SOURCE_NAME?></a> (poslední aktualizace: <?=$posledni_aktualizace?>)</span>
  	
  	<br>
  	
  	<span class="copy">Vizualizace: <a href="https://cz.linkedin.com/in/martinkopecek">Martin Kopeček</a>, <a href="https://cz.linkedin.com/in/benediktkotmel">Benedikt Kotmel</a>, <a href="https://cz.linkedin.com/in/janvlasaty">Jan Vlasatý</a>; &copy; 2015 MFČR</span>
    
  	<br>
  	
  	<a href="<?=WEB_ROOT?>/o-projektu.php" id="about2">O projektu</a>
  	<?php if(false): ?>| <a href="/stranka/api">API</a><?php endif; ?>
    
    
  </div>
</div>