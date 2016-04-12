<?php

  $profiles = $GLOBALS["profiles"];

  function create_profile_url($profile,$dataset = null){
    $url = explode("?",$_SERVER["REQUEST_URI"]);
    //return $url[0]."?profil=$profile&dataset=$dataset";
    return "?profil=$profile&dataset=$dataset";
  }  
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="author" content="Martin Kopeček (https://cz.linkedin.com/in/martinkopecek), Benedikt Kotmel (https://cz.linkedin.com/in/benediktkotmel), Jan Vlasatý (https://cz.linkedin.com/in/janvlasaty)">
		
    <link rel="license" hreflang="cs" href="https://jxself.org/translations/gpl-3.cz.shtml">
    <link rel="license" hreflang="en" href="https://www.gnu.org/licenses/gpl-3.0.txt">
    <link rel="author" href="https://cz.linkedin.com/in/martinkopecek">
    
		<?php if(@$share_url): ?><meta property="og:url" content="<?=$share_url?>"><?php endif; ?>
		<meta property="og:image" content="">
		<meta property="og:title" content="<?=@$title ? "$title :: " : ""?><?=TITLE?>">
		<meta property="og:description" content="">

		<title><?=@$title ? "$title :: " : ""?><?=TITLE?></title>

		<!-- STYLES -->
		
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
		
		<!-- jQuery -->
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?=STATIC_ROOT?>/lib/jquery/jQRangeSlider/jQAllRangeSliders-classic-min.css">
		
		<!-- fancyBox -->
		<link rel="stylesheet" href="<?=STATIC_ROOT?>/lib/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />
		
		<!-- theme -->
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
		<link rel="stylesheet" href="<?=STATIC_ROOT?>/css/style-default.css">
		
		<?=@$styles?>
		
		<!-- SCRIPTS -->
		
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
		<![endif]-->
		
		<!-- jQuery -->
		<script src="<?=STATIC_ROOT?>/lib/jquery-1.11.3.min.js"></script>
		<script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>
		
		<!-- History.js -->
		<script src="<?=STATIC_ROOT?>/lib/native.history.js"></script>
		
		<!-- fancyBox -->
		<script type="text/javascript" src="<?=STATIC_ROOT?>/lib/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
		
		<script src="<?=STATIC_ROOT?>/lib/jquery/jQRangeSlider/jQAllRangeSliders-min.js"></script>
		<script src="<?=STATIC_ROOT?>/lib/raphael-min.js"></script>
		<script src="<?=STATIC_ROOT?>/lib/raphael-style.js"></script>
		
		<script type="text/javascript">
			
			var data = <?=json_encode(array("view" => @$_GET["view"] ?: "index","skupina" => @$_GET["skupina"], "page" => @$_GET["page"] ?: 1, "dodavatel" => @$_GET["dodavatel"]))?>;
			
			History.replaceState(data,"");
			
			var WEB_ROOT = <?=json_encode(WEB_ROOT)?>;
			var API_ROOT = <?=json_encode(API_ROOT)?>;
			var TITLE = <?=json_encode(TITLE)?>;
      
      $(document).ready(function(){
      
      $("#about,#about2").fancybox({
        type:"iframe",
        href:"<?=WEB_ROOT?>/o-projektu.php?popup=1",
        width:600,
        height:600,
        padding:40
      });
      
      });
			
		</script>
		
		<?=@$scripts?>
	</head>
	<body>
		<!-- NAVBAR -->
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<!-- Brand and toggle get grouped for better mobile display -->
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?=WEB_ROOT?>/">
						<svg version="1.1" id="Vrstva_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
							 viewBox="0 0 86 103" enable-background="new 0 0 86 103" xml:space="preserve" height=35>
							<g id="XMLID_1_">
								<polygon id="XMLID_11_" opacity="0.75" fill-rule="evenodd" clip-rule="evenodd" fill="#2db9ff" points="43,0 86,24 43,47 	"/>
								<polygon id="XMLID_10_" opacity="0.5" fill-rule="evenodd" clip-rule="evenodd" fill="#2db9ff" points="0,24 43,47 43,103 0,80 	
																																	 "/>
								<polygon id="XMLID_12_" opacity="0.35" fill-rule="evenodd" clip-rule="evenodd" fill="#2db9ff" points="86,80 86,24 43,47 43,103 	
																																	  "/>
								<polygon id="XMLID_14_" opacity="0.95" fill-rule="evenodd" clip-rule="evenodd" fill="#2db9ff" points="43,0 28,39 43,47 	"/>
								<polygon id="XMLID_15_" opacity="0.85" fill-rule="evenodd" clip-rule="evenodd" fill="#2db9ff" points="0,24 28,39 43,0 	"/>
							</g>
						</svg>
					</a>
				</div>

				<!-- Collect the nav links, forms, and other content for toggling -->
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        
          <ul class="nav navbar-nav">
            <li><a class="navbar-brand" href="<?=WEB_ROOT?>/">Supervizor</a></li>
            <li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="font-size:18px;"><?=$profiles[PROFILE_ID]["title"]?> <span class="caret"></span></a>
							<ul class="dropdown-menu">
              <?php foreach($profiles as $profile_id => $profile): ?>
								<li><a href="<?=create_profile_url($profile_id)?>"><?=$profile["title"]?></a></li>
              <?php endforeach; ?>
							</ul>
						</li>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false" style="font-size:18px;"><?=$profiles[PROFILE_ID]["datasets"][PROFILE_DATASET]["title"]?> <span class="caret"></span></a>
							
              
              <ul class="dropdown-menu">
                <?php foreach($profiles[PROFILE_ID]["datasets"] as $dataset_id => $dataset): ?>
                <li><a href="<?=create_profile_url(PROFILE_ID,$dataset_id)?>"><?=$dataset["title"]?></a></li>
                <?php endforeach; ?>
							</ul>
              
						</li>
					</ul>
          
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">O projektu <span class="caret"></span></a>
							<ul class="dropdown-menu">
								<li><a href="<?=WEB_ROOT?>/o-projektu.php" id="about">O projektu</a></li>
                <li><a href="https://github.com/SmallhillCZ/Supervizor">Supervizor na GitHubu</a></li>
								<li><a href="http://data.mfcr.cz">Otevřená data MFČR</a></li>
							</ul>
						</li>
					</ul>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</nav>
		
		<!-- MAIN -->
		<div id="main">

    <?php if(@$full): ?>
      <?=$body?>
      
    <?php else: ?>
    	<div id="left">
    		<?=@$left?>
    	</div>
    	<div id="right">
    		<?=@$body?>
    	</div>
      
    <?php endif; ?>

		</div>
		
		<?php include "footer.php"; ?>
    
    <script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  
    ga('create', 'UA-58619008-1', 'auto');
    ga('send', 'pageview');
  
  </script>
	</body>
</html>
