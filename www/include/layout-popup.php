<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<meta name="author" content="Martin Kopeček">

		<title>Faktury MF</title>

		<!-- STYLES -->

		<link href='http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,400,300,600&subset=latin,latin-ext' rel='stylesheet' type='text/css'>

		<!-- jQuery -->
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="<?=STATIC_ROOT?>/lib/jquery/jQRangeSlider/jQAllRangeSliders-classic-min.css">

		<!-- fancyBox -->
		<link rel="stylesheet" href="<?=STATIC_ROOT?>/lib/fancybox/source/jquery.fancybox.css?v=2.1.5" type="text/css" media="screen" />

		<style type="text/css">
			
			body{margin:0;}
			#footer{border-top:1px solid #999;color:#aaa;text-align:center;font-size:.8em;margin-top:50px;padding-top:5px;}
			#footer a{color:#aaa;}
			#footer a:hover{color:rgb(51, 122, 183);}
		</style>
		<?=@$styles?>

		<!-- SCRIPTS -->

		<!-- jQuery -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/jquery-ui.min.js"></script>

		<!-- fancyBox -->
		<script type="text/javascript" src="<?=STATIC_ROOT?>/lib/fancybox/source/jquery.fancybox.pack.js?v=2.1.5"></script>

		<script src="<?=STATIC_ROOT?>/lib/jquery/jQRangeSlider/jQAllRangeSliders-min.js"></script>
		<script src="<?=STATIC_ROOT?>/lib/raphael-min.js"></script>
		<script src="<?=STATIC_ROOT?>/lib/raphael-style.js"></script>


		<?=@$scripts?>
	</head>
	<body>
		<div id="main">
			<?=@$body?>

		</div>
		<!-- Facebook Share -->
		<div id="fb-root"></div>
		<script>(function(d, s, id) {
			var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v2.3&appId=200297620132275";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		</script>
	</body>
</html>