<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- STYLES -->
<link rel="icon" href="<?=IMG?>icons/favicon.ico" type="image/x-icon">
<?=$this->addStyle('master', 'media="screen"')?>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/smoothness/jquery-ui.css" />
<?=$this->addStyle('bootstrap.min', 'media="screen"')?>
<?=$this->addStyle('bootstrap-theme.min', 'media="screen"')?>
<?=$this->addStyle('FontAwesome.min', 'media="screen"')?>
<!--<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">-->
<?=$this->addStyle('media', 'media="screen"', false)?>
<?=$this->addStyle('b2b', 'media="screen"', false)?>
<link rel="stylesheet" type="text/css" href="<?=JS?>fancybox/jquery.fancybox.css?v=2.1.4" media="screen" />
<link rel="stylesheet" type="text/css" href="<?=JS?>fancybox/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
<link rel="stylesheet" type="text/css" href="<?=JS?>slick/slick.css"/>

<!-- JS's -->
<?=$this->addJS('//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js',true)?>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.min.js"></script>
<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
<?=$this->addJS('bootstrap.min',false)?>
<?=$this->addJS('master',false,false)?>
<?=$this->addJS('pageOpener',false,false)?>
<?=$this->addJS('user',false,false)?>
<?=$this->addJS('jquery.cookie',false)?>
<script type="text/javascript" src="<?=JS?>slick/slick.min.js"></script>
<script type="text/javascript" src="<?=JS?>fancybox/jquery.fancybox.js?v=2.1.4"></script>
<script type="text/javascript" src="<?=JS?>fancybox/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
 <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&language=hu"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">
	$(function(){
		$('*[jOpen]').openPage({
			overlayed 	: true,
			path 		: '<?=AJAX_BOX?>'
		});
	})
	function searchItem(e){
		var srcString = e.find('input[type=text]').val();

		$.post('<?=AJAX_POST?>',{
			type: 'log',
			mode: 'searching',
			val: srcString
		},function(re){
			document.location.href='/kereses/'+srcString;
		},"html");
	}
</script>
