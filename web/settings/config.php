<?
	// Domain név
	define('DM', 'arena.hu');
	define('DOMAIN','http://'.$_SERVER['HTTP_HOST'].'/');
	define('MDOMAIN',$_SERVER['HTTP_HOST']);
	define('CLR_DOMAIN',str_replace(array("http://","www."),"",substr('www.'.DOMAIN,0,-1)));	
	define('AJAX_GET','/ajax/get/');
	define('AJAX_POST','/ajax/post/');
	define('AJAX_BOX','/ajax/box/');
	define('CLORADE_API_IF', 'http://mail.arena.hu/aclowin');
	define('GA_REMARKETING', true);
	
	// Facebook APP
	define('FBAPPID','114468722051781');
	define('FBSECRET','abde731025555f2f290fcf618a52a0ed');
	
	// Időzóna beállítása
	date_default_timezone_set('Europe/Berlin');
	
	////////////////////////////////////////
	// Ne módosítsa innen a beállításokat //
	// PATHS //
		define('TEMP','v1.0');
		
		define('PATH', realpath($_SERVER['HTTP_HOST']));
		
		define('APP_PATH','application/');
		
		define('LIBS','/web/arena/admin/'.APP_PATH . 'libs/');
	
		define('MODEL',APP_PATH . 'models/');
	
		define('VIEW',APP_PATH . 'views/'.TEMP.'/');
	
		define('CONTROL',APP_PATH . 'controllers/');
		
		define('STYLE','/src/css/');
		define('SSTYLE','/public/'.TEMP.'/styles/');
		
		define('JS','/src/js/');
		define('SJS','/public/'.TEMP.'/js/');
		
		define('UPLOADS','http://cp.'.DM.'/src/uploads/');
		define('IMG','http://cp.'.DM.'/src/images/');		
	// Környezeti beállítások //
	
		define('SKEY','sdfew86f789w748rh4z8t48v97r4ft8drsx4');
			
		define('NOW',date('Y-m-d H:i:s'));
		
		define('PREV_PAGE',$_SERVER['HTTP_REFERER']);
		
	// Adminisztráció
	
		define('ADMROOT','http://cp.'.DM.'/');
	
	require "data.php";
?>