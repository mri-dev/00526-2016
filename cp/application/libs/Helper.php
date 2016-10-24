<?
	class Helper {

		static function GET(){
			$b = explode("/",rtrim($_GET[tag],"/"));
			if($b[0] == null){ $b[0] = 'home'; }
			return $b;
		}
		static function getArrayValueByMatch($data, $prefix){

			$return = array();
			foreach($data as $dk => $dv){
				if(strpos($dk,$prefix) === 0){
					$return[str_replace($prefix,'',$dk)] = $dv;
				}
			}

			return $return;
		}
		static function currentPageNum(){
		  $num 	= 0;
		  $last = self::getLastParam();

		  $num 	= (is_numeric($last)) ? $last : 1;

		  return $num;
		}

		static function getParam($arg = array()){
			$get = self::GET();

			if(!empty($arg)){
				$pos = 2;
				foreach($arg as $ar){
					if($get[$pos] != null){
						$param[$ar] = $get[$pos];
						$pos++;
					}else{ break; }
				}
			}else{
				$pos = 0;
				foreach($get as $g){
					if($pos > 1){
						$param[] = $g;
					}
					$pos++;
				}
			}

			return $param;
		}

		static function getLastParam(){
			$p = self::GET();
			$p = array_reverse($p);
			return $p[0];
		}

		static function welcomeEco(){
			$now = NOW;
			$str = '';
			$inow = substr($now,11,2);
			$inow = (int)$inow;

				if($inow >= 0 && $inow < 6 ){
					$str = 'Szép estét';
				}else if($inow >= 6 && $inow < 9){
					$str = 'Jó reggelt';
				}else if($inow >= 9 && $inow < 18){
					$str = 'Szép napot';
				}else if($inow >= 18 && $inow <= 23){
					$str = 'Szép estét';
				}

			return $str;
		}

		static function cashFormat($cash){
			$cash = number_format($cash,0,""," ");
			return $cash;
		}

		static function makeSafeUrl($str,$after){
			$f 		= array(' ',',','á','Á','é','É','í','Í','ú','Ú','ü','Ü','ű','Ű','ö','Ö','ő','Ő','ó','Ó','(',')','\'','"',"=","/","\\","?","&","!");
			$t 		= array('-','','a','a','e','e','i','i','u','u','u','u','u','u','o','o','o','o','o','o','','','','','','','','','','');
			$str 	= str_replace($f,$t,$str);
			$str 	= strtolower($str);

			$ret = $str . $after;
			return $ret;
		}
		static function dellAllOfFolder($root){
			$f = new DirectoryIterator($root);

			 // Összes fájl törlése
			 foreach ($f as $ff) {
		        if ($ff->isFile()) {
					$ffn = $ff->getFilename();
					unlink($root.$ffn);
		        }
		    }

			// Mappa törlése
			rmdir($root);
		}

		static function removeFile($file){
			if(file_exists($file)){
				unlink($file);
				return true;
			}else return false;
		}

		static function getFileRoot($file){
			$ct 	= explode("/",$file);
			$max 	= count($ct);
			$im 	= $ct[$max-1];
			$root 	= str_replace($im,"",$file);
			return $root;
		}
		static function arry(&$array, $key, $type = 'ASC') {
		    $sorter=array();
			$ret=array();
				reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
			$array=$ret;
			if($type == "DESC"){
				$array = array_reverse($array);
			}
		}

		static function dellFromArrByVal($arr,$dell){
			if(($key = array_search($dell, $arr)) !== false) {
			    unset($arr[$key]);
			}

			return $arr;
		}

		static function randomPassword( $maxlen = 12 ) {
		    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		    $pass = array(); //remember to declare $pass as an array
		    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		    for ($i = 0; $i < $maxlen; $i++) {
		        $n = rand(0, $alphaLength);
		        $pass[] = $alphabet[$n];
		    }
		    return implode($pass); //turn the array into a string
		}

		static function dellFromArrByAssocVal($arr,$by,$v){
			foreach($arr as $key => $ar){
				if($ar[$by] == $v){
					$rmkey = $key;
				}
			}

			unset($arr[$rmkey]);

			return $arr;
		}

		static function getFromArrByAssocVal($arr,$by,$val){
			foreach($arr as $key => $ar){
				if($ar[$by] == $val){
					$rmkey = $key;
				}
			}

			return $rmkey;
		}

		public static function setMashineID(){
			if(self::getMachineID() == ""){
				setcookie('__mid',mt_rand(),time() + 60*60*24*365*2,"/");

				if($_COOKIE['__mid'] != ""){
					header('Location: ');
				}
			}
		}

		public static function getMachineID(){
			return $_COOKIE['__mid'];
		}

		static function colorizeStr($str){
			// Színek
			$colors = array(
				"---------" => '#fb8200',
				"Aktív" => '#62b017',
				"Aktiválva" => '#62b017',
				"Látható" => '#62b017',
				"Engedélyezve" => '#62b017',
				'Inaktív' => '#be3535',
				'Tiltva' => '#be3535',
				'Rejtve' => '#be3535',
				'Felülvizsgáltnak jelölés' => '#f08026',
				'Felülvizsgálatlan' => '#f08026',
				'Felülvizsgálva' => '#65b017'
			);


			$clr = $colors[$str];
			return '<span style="color:'.$clr.';">'.$str.'</span>';
		}

		static function softDate($d){
			if($d == '0000-00-00 00:00:00' || is_null($d)){ return 'n.a.'; }
			return str_replace("-","/",substr($d,0,-3));
		}

		static function txtModular($txt){
			// Modulok
			$txt = Youtube::ember($txt);

			return $txt;
		}

		static function XMLParse($file){
			$xmlstr = file_get_contents($file);
			$xml = new SimpleXMLElement($xmlstr);
			return $xml;
		}

		static function JSONParse($file_or_url){
			$back 		= array();

			$content 	= file_get_contents($file_or_url);
			$parse   	= json_decode($content, true);

			return $parse;
		}

		static function emailPatern($str){
			$patern = '
			<html>
				<head>
					<title>{TEMA_NEV}</title>
				</head>
				<body style="margin:0; padding:0;" bgcolor="#f0f0f0">
					<div class="mail" style="margin:35px; background-color:#ffffff; border:2px solid #ffb007; padding:0; width:800px; color:#4f565a;">
						<table class="mail" width="800" cellspacing="0" cellpadding="5">
							<tr style="color:#5c5c5c;">
								<td align="left"><a title="'.TITLE.'" href="'.DOMAIN.'"><img src="http://img.goldfishing.hu/i/Logo_250_white.png" alt="'.DOMAIN.'" /></a></td>
								<td align="right">
									<div style="font-size:20px; font-weight:bold; color:#222;">{TEMA_NEV}</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="left">
									<div style="padding:10px; color:#403d3d;">
									{UZENET}
									</div>
								</td>
							</tr>
							<tr bgcolor="#2b2b2b">
								<td align="left">
									<div style="font-size:10px; color:#818181;">{ALAIRAS}</div>
								</td>
								<td align="right">
									<div style="font-size:14px; font-weight:bold; color:#f6d124;">'.MDOMAIN.'</div>
								</td>
							</tr>
						</table>
					</div>';
					if($str['NEWS']){
						$patern .= '<div align="justify" style="color:#9c9c9c; font-size:10px; margin:35px; width:800px;">Nagyon köszönjük, hogy megtisztelt levelünk végigolvasásával! A gazdasági reklámtevékenység alapvető feltételeiről és egyes korlátairól szóló 2008. évi XLVIII. törvény 6. §-ának maximális figyelembevételével, abban reménykedve, hogy Magyarországon egyedülálló szolgáltatást nyujtva, hozzá tudunk járulni az Ön sikereihez. A kapcsolati adataikat Cégnyilvántartásból származó, szabadon elérhető nyilvános cégadatok, cégnév, székhely, ügyvezető, e-mail cím felhasználásával nyertük ki. Nem kívánjuk munkáját rendszeres e-mailekkel zavarni, levelünk célja csupán a lehetőségekre hívja fel a figyelmet, amennyiben nem járul hozzá, hogy a jövőben esetleg levelet küldjünk Önnek, <a href="'.DOMAIN.'maillist/usm/'.$str['EMAIL'].'">kattintson ide</a> és Mi természetesen tiszteletben fogjuk tartani döntését.</div>';
					}
					$patern .= '</body>
			</html>
			';

			$filledData = str_replace(
				array('{UZENET}','{ALAIRAS}','{TEMA_NEV}'),
				array($str['UZENET'],$str['ALAIRAS'],$str['TEMA_NEV']),
				$patern
			);

			return $filledData;
		}


		static function sendEmail($sender,$to,$subj,$str = array()){

			if($str[msg] == ""){
				throw new Exception("Az email üzenet része üres!");
			}

			$text = nl2br($str[msg]);

			$text = self::emailPatern(array(
				"UZENET" 	=> $text,
				"ALAIRAS" 	=> $str['alairas'],
				'TEMA_NEV' 	=> $str['megnevezes']
			));

			$kuldo='=?utf-8?B?'.base64_encode(TITLE).'?= <'.$sender.'>';
		  	$tema='=?utf-8?B?'.base64_encode($subj).'?=';
		  	$fejlec = "FROM: ".$kuldo."\r\n";
		  	$fejlec .= "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=utf-8\r\n";
		  	$fejlec .= "Reply-To:".$kuldo."\r\n";
		  	$fejlec .= "X-Mailer: PHP/" . phpversion() . "\r\n";
			if(!empty($str[bcc])){
				$fejlec .= 'BCC: '. implode(",", $str[bcc]) . "\r\n";
			}
		  	$fejlec .= "X-Sender-IP: ".$_SERVER["REMOTE_ADDR"]."\r\n";
		  	$fejlec .= "Content-Transfer-Encoding: 8bit\r\n\r\n";

		  	if(@mail($to,$subj,$text,$fejlec)){}else{
				error_log('Nem sikerült kiküldeni a levelet az ügyfél részére');
			}
		}

        static function getNavigatorNextPrev($info_arr){
            $data = array();
            $next = 1;
            $prev = 1;

            $i = $info_arr[pages];

            if(!empty($i)){
                extract($i);

                if($current == 1){
                    $prev = 1;
                    $next = 2;
                }else if($current > 1 && $current < $max){
                    $prev = $current - 1;
                    $next = $current + 1;
                }else if($current == $max){
                    $prev = $max - 1;
                    $next = $max;
                }
            }

            $data[next] = $next;
            $data[prev] = $prev;

            return $data;
        }

		static function getSecureUrlKey($anc = ''){
			$anc = ($anc != "") ? '#'.$anc : '';
			$s = base64_encode(substr(DOMAIN,0,-1).$_SERVER['REQUEST_URI'].$anc);
			return $s;
		}

		static function getPrevPage(){
			$url = $_SERVER['REQUEST_URI'];
			$xurl = explode("/",trim($url,"/"));
			$xurl = array_reverse($xurl);
			$url = str_replace($xurl[0].'/',"",$url);
			return $url;
		}

		static function admAlertEmail($sbj,$txt,$aurl = false){

			$subj 	= "Értesítés: ".$sbj;
			$sender = 'noreply@'.CLR_DOMAIN;
			$text 	= nl2br($txt);
			if($aurl){
				$aurl = DOMAIN.$aurl;
				$text 	.= '<br />Kapcsolódó link: <a href="'.$aurl.'">'.$aurl.'</a>';
			}

			$to 	= ALERT_EMAIL;

			$kuldo='=?utf-8?B?'.base64_encode(TITLE.' értesítő').'?= <'.$sender.'>';
		  	$tema='=?utf-8?B?'.base64_encode($subj).'?=';
		  	$fejlec = "FROM: ".$kuldo."\r\n";
		  	$fejlec .= "MIME-Version: 1.0\r\n" . "Content-type: text/html; charset=utf-8\r\n";
		  	$fejlec .= "Reply-To:".$kuldo."\r\n";
		  	$fejlec .= "X-Mailer: PHP/" . phpversion() . "\r\n";
		  	$fejlec .= "X-Sender-IP: ".$_SERVER["REMOTE_ADDR"]."\r\n";
		  	$fejlec .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		  	if(@mail($to,$subj,$text,$fejlec)){}
		}

		private static function pageNavigator($next_page){
			$last = self::getLastParam();
			$next_page = (is_numeric($last)) ? $next_page : substr(DOMAIN,0,-1).$_SERVER['REQUEST_URI'].'/'.$next_page;
			return $next_page;
		}
		/**
         * Oldal listázást hoz létre.
         *
         * @param array $page_arry A lekérdezés nyers "page" result tömb információja. Pl.: $result[info][pages]
         * @return void
		*/
		static function makeNavigator($page_arry){
			$max 	= (int)$page_arry[max];
			$c 		= (int)$page_arry[current];

			echo '<ul class="navi">';
			// Oldalak
			for($s = 1; $s <= $max; $s++){
				echo '<li class="';
					if($s == $c){echo 'on';}
				echo '"><a href="'.$s.'">';
				echo $s;
				echo '</a></li>';
			}
			echo '</ul>';
		}

		static function pagePosition($page_arry){
            $max = $page_arry[max];
            if($max <=0) $max = 1;
			echo '<strong>'.$page_arry[current].'</strong>. '.__('oldal').' / <strong>'.$max.' '.__('összesen').'</strong>';
		}


		static function safeEmail($email){
			$email = str_replace(array('@','.'),array(' <em>(kukac)</em> ',' <em>(pont)</em> '),$email);
			return $email;
		}
		/* ADD: 2013/07 */
		static function getSourceStr($str_source){
			$ret = array();
			$str_source = rtrim($str_source,";");
			$str_source = ltrim($str_source,";");

			$cut = explode(";",$str_source);

			foreach($cut as $c){
				$ec = explode(":",$c);
				$ret[$ec[0]] = $ec[1];
			}

			return $ret;
		}
		/**
		 * POST metódus szöveg formátum eltárolása hiba esetén
		 *
		 * @param string $which Eltárolandó POST metódus neve
		 * @param string $post_data_string JSON POST metódus szöveg
		 *
		 * @return void
		 *
		 * @since 2013/07
		 */
		static function setStoredPOSTData($which, $post_data_string) {
			setcookie('__'.$which.'poststr',$post_data_string, time()+60*60*24,'/');
		}

		/**
		 * POST metódus tároló törlése
		 *
		 * @param string $which Eltárolandó POST metódus neve
		 *
		 * @return void
		 *
		 * @since 2013/07
		 */
		static function dellStoredPOSTData($which) {
			setcookie('__'.$which.'poststr','', time()-60,'/');
		}

		/**
		 * POST metódus adatainak szöveggé alakítása
		 *
		 * @return strong JSON POST szöveg
		 *
		 * @since 2013/07
		 */
		static function getPOSTData() {
			if(!empty($_POST)){
				$post = $_POST;
				$post = json_encode($post);
				return $post;
			}else{
				return false;
			}
		}

		/**
		 * Tárolt POST metódus szöveg vissza alakítása
		 *
		 * @param string $which Az eltárolt POST metódus neve
		 *
		 * @return array POST metódis adatai
		 *
		 * @since 2013/07
		 */
		static function getbackPOSTData($which) {
			$what 	= '__'.$which.'poststr';
			$str 	= $_COOKIE[$what];

			if(isset($str)){
				$str 	= stripslashes($str);
				$back 	= json_decode($str, true);
				return $back;
			}else{
				return false;
			}
		}

		static function makeAlertMsg($type, $str){
            /*
             * Required: Bootstrap 3 (http://getbootstrap.com/components/#alerts)
             * */
            switch($type){
                case 'pWarning':
                    return '
                    <div class="alert alert-warning alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Figyelem!').'</strong><br/>
                      '.$str.'
                    </div>';
                break;
                case 'pError':
                    return '
                    <div class="alert alert-danger alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Hiba történt!').'</strong><br/>
                      '.$str.'
                    </div>';
                    break;
                case 'pInfo':
                    return '
                    <div class="alert alert-info alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Információ:').'</strong><br/>
                      '.$str.'
                    </div>';
                    break;
                case 'pSuccess':
                    return '
                    <div class="alert alert-success alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      '.$str.'
                    </div>';
                    break;
                default:
                    return '<div class="'.$type.'">'.$str.'</div>';
                break;
            }
		}

		static function getPostPrefData($input, $prefix){
			$ret = array();

			if(count($input) != 0):
				$prefix = ($prefix != '') ? $prefix.'_' : '';
				foreach($input as $inp_by => $inp_val){
					$ret[str_replace($prefix,'',$inp_by)] = $inp_val;
				}
			endif;

			return $ret;
		}

		static function getShowBg($by, $def_bg = ''){
			$bg = substr(IMG,1).$def_bg;
			$exist = file_exists($bg);

			if($def_bg == '' || ($def_bg != '' && !$exist)){
				switch($by){
					default:
						return 'background:url('.IMG.'box-img-sample-orange.jpg) no-repeat center;';
					break;
					case 'Szórakozóhely':
						return 'background:url('.IMG.'box-img-sample-night_light.jpg) no-repeat center;';
					break;
					case 'Kocsma':
						return 'background:url('.IMG.'box-img-sample-night_light.jpg) no-repeat center;';
					break;
					case 'Étterem':
						return 'background:url('.IMG.'box-img-sample-restaurant.jpg) no-repeat center;';
					break;
					case 'Pizzázó':
						return 'background:url('.IMG.'box-img-sample-pizza.jpg) no-repeat center;';
					break;
				}
			}else{
				return 'background:url('.IMG.str_replace('.jpg','_blured.jpg',$def_bg).') no-repeat center; background-size: 100%;';
			}
		}

		static function distanceDate($date = NOW){
			if($date == '0000-00-00 00:00:00' || empty($date) ){ return 'sose'; }
			$now 		= strtotime(NOW);
			$date 		= strtotime($date);
			$mode 		= 'past';
			if($date < $now){
				$dif_sec =  $now - $date ;
			}else{
				$mode = 'future';
				$dif_sec =  $date - $now ;
			}

			$ret 		= '';
			///////////////////////////////
			$perc 	= 60;
			$ora 	= $perc * 60;
			$nap 	= $ora * 24;
			$honap 	= $nap * 30;
			$ev 	= $honap * 12;
			///////////////////////////////
				switch($mode){
					case 'past':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperce');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perce');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('órája');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							if($np == 1){
								$ret = __('tegnap');
							}else
								$ret = $np.' '.__('napja');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónapja');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('éve');
						}
					break;
					case 'future':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperc');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perc');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('óra');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							$ret = $np.' '.__('nap');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónap');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('év');
						}
					break;
				}


			return $ret;
		}

		static function getVoteImg($type, $score){
			$img = 'rate-';

			switch($type){
				case 'star':
					$img .= 'star-';
				break;
			}

			if($score == '' || $score == 0 || !is_numeric($score)){
				$img .= 'novote';
			}else if($score > 0 && $score < 1){
				$img .= '01';
			}else if($score == 1){
				$img .= '1';
			}else if($score > 1 && $score < 2){
				$img .= '12';
			}else if($score == 2){
				$img .= '2';
			}else if($score > 2 && $score < 3){
				$img .= '23';
			}else if($score == 3){
				$img .= '3';
			}else if($score > 3 && $score < 4){
				$img .= '34';
			}else if($score == 4){
				$img .= '4';
			}else if($score > 4 && $score < 5){
				$img .= '45';
			}else if($score >= 5){
				$img .= '5';
			}

			$img .= '.png';

			return IMG.$img;
		}

		static function starRating($num, $uname){
			echo '<span class="rate">';
			for($i=$num; $i>=1; $i--){
				echo '
				<input type="radio" class="rating-input" id="rating-input-'.$uname.'-'.$i.'" name="rating-input-'.$uname.'" value="'.$i.'">
				<label for="rating-input-'.$uname.'-'.$i.'" class="rating-star"></label>';
			}
			echo '</span>';
		}

		static function getMonthByNum($mnum){
			$re = $mnum;
				switch($mnum){
					case 1:
						$re = __('január');
					break;
					case 2:
						$re = __('február');
					break;
					case 3:
						$re = __('március');
					break;
					case 4:
						$re = __('április');
					break;
					case 5: case 'May':
						$re = __('május');
					break;
					case 6:
						$re = __('junius');
					break;
					case 7:
						$re = __('július');
					break;
					case 8:
						$re = __('augusztus');
					break;
					case 9:
						$re = __('szeptember');
					break;
					case 10:
						$re = __('október');
					break;
					case 11:
						$re = __('november');
					break;
					case 12:
						$re = __('december');
					break;

				}
			return $re;
		}

		static function get_extension($file_name){
			$ext = explode('.', $file_name);
			$ext = array_pop($ext);
			return strtolower($ext);
		}

		static function exit_status($str){
			echo json_encode(array('status'=>$str));
			exit;
		}

		static function streamTypeIcon($type = null){
			$def = 'default';
			$pre = IMG.'stream-icons/';
			$def_img = $pre.$def.'.png';


			if(is_null($type) || $type == ''){ return $def_img; }

			if(!file_exists(substr($pre,1).$type.'.png')){ return $def_img; }

			return IMG.'stream-icons/'.$type.'.png';
		}

		static function placeRateNum($point, $clear = false){
			$cmt = '';
			if($point == '' || is_null($point)){
				$tp 	= 'n.a.';
				$title 	= __('még nem értékelte senki a helyet');
			}else{
				$max_rate_type 	= (int)count(json_decode(PLACE_RATE));
				$max_rate_star 	= (int)PLACE_RATE_TOTAL_STAR;
				$max_point 		= (int)($max_rate_type * $max_rate_star);
				$total_point 	= ( ($point / ($max_point / 100)) / 10 );
				$tp = number_format($total_point,1,'.','');
				//////////////////
				$title = '10 / '.$tp;
				$cmt = "<div>".__('pont')."</div>";
			}

			if(is_numeric($tp)){
				if($tp > 0 && $tp <= 5){
					$class = 'orange';
				}else if($tp > 5 && $tp <= 8){
					$class = 'lightergreen';
				}else if($tp > 8){
					$class = 'green';
				}
			}

			$ret = '<div class="ratePoint '.$class.'" title="'.$title.'"><table cellspacing="0" cellpadding="0" align="center">
				<tr>
					<td height="">'.$tp.$cmt.'</td>
				</tr>
			</table></div>';

			if($clear){
				$ret = $tp;
			}

			return $ret;
		}
		static function getProfilImg($img = '', $arg = array()){
			$arg[cache] = (!$arg[cache]) ? true : $arg[cache];
			$arg[width] = (!$arg[width]) ? 50 : $arg[width];
			$defi = ($arg[def]) ? $arg[def] : 'no-profil-img.jpg';

			$iroot = CLR_DOMAIN.IMG;
			if($arg[stored]){
				switch($arg[stored]){
					case 'ittettunk':
						$iroot = PLACE_IMG;
					break;
				}
			}

			if(strpos($img,'facebook') !== false){
				$arg[cache] = false;
			}

			if($img == '' || is_null($img)){
				return 'http://images.weserv.nl/?url='.CLR_DOMAIN.IMG.$defi.'&w='.$arg[width].'&h='.$arg[width].'&t=square';
			}

			if($arg[cache]){
				$img 	= str_replace('http://','',$img);
				if(strpos($img,CLR_DOMAIN) !== 0) $img = $iroot.$img;
				$re_img = 'http://images.weserv.nl/?url='.$img.'&w='.$arg[width].'&h='.$arg[width].'&t=square';
			}else{
				$re_img = $img;
			}
			return $re_img;
		}

		static function createPageNavi($eles = array()){
			if(!empty($eles)){
				echo '<ul class="pagenavi">';
					echo '<li><a href="/">&nbsp;</a></li>';
					foreach($eles as $elem):
						echo '<li><a href="'.$elem[to].'">'.$elem[text].'</a></li>';
					endforeach;
				echo '</ul>';
			}else return false;
		}

		static function hashTag($str = '', $arg = array()){
			$pre_tag = ($arg[pretag]) ? $arg[pretag] : 'ittettunk';
			if($str == '') return '';
			$fstr = '';
			$xstr = explode(" ",trim($str));
				foreach($xstr as $sx){
					$fstr .= trim(ucfirst(str_replace(array('-','.'),'',strtolower(self::makeSafeUrl($sx,'')))));
				}
			$fstr = $pre_tag.$fstr;
			return '#'.$fstr;
		}

		static function smtpMail($arg = array()){
			if(is_array($arg[recepiens]) && count($arg[recepiens]) > 0){

				date_default_timezone_set('Europe/Budapest');

				$mail = new PHPMailer;
				$news = ($arg[news]) ? true : false;
				$from = ($arg[from]) ? $arg[from] : EMAIL;
				$fromName = ($arg[fromName]) ? $arg[fromName] : TITLE;

				$mail->isSMTP();                    // Set mailer to use SMTP
				//$mail->Host 		= '';
				$mail->SMTPDebug 	= ($arg[debug]) ? $arg[debug] : 0;


				$mail->SMTPAuth 	= true;         // Enable SMTP authentication
				$mail->SMTPSecure 	= ($arg[smtp_mode])?$arg[smtp_mode]:SMTP_MODE;    // Enable encryption, 'ssl' also accepted
				$mail->Host 		= SMTP_HOST;  	// Specify main and backup server
				$mail->Port 		= ($arg[smtp_port])?$arg[smtp_port]:SMTP_PORT;
				$mail->Username 	= SMTP_USER;    // SMTP username
				$mail->Password 	= SMTP_PW;      // SMTP password

				//echo $mail->Username.':'.$mail->Password.' @ '.$mail->Host;

				$mail->From 		= $from;
				$mail->FromName 	= $fromName;
				$mail->addReplyTo($from, $fromName);
				$inserted 			= array();
				$err 				= array();
				$ret 				= array();

				foreach($arg[recepiens] as $r){
					$mail->addAddress($r);
					$mail->WordWrap = 150;                                 // Set word wrap to 50 characters
					$mail->isHTML(true);                                  // Set email format to HTML

					$msg = Helper::emailPatern(array(
						'UZENET' 	=> $arg[msg],
						'ALAIRAS' 	=> $arg[alairas],
						'TEMA_NEV' 	=> $arg[tema],
						'NEWS' 		=> $news,
						'EMAIL' 	=> $r
					));

					$mail->Subject = $arg[sub];
					$mail->Body    = $msg;
					$mail->AltBody = $mail->html2text($msg);

					if (!$mail->send()) {
				       	$emsg 	=  "Kiküldés sikertelen: (" . str_replace("@", "&#64;", $r) . ') ' . $mail->ErrorInfo . '<br />';
						$err[] 	= array('mail' => $r, 'msg' => $emsg);
				        break;
				    }else{
				        $inserted[] = $r;
				    }

					$mail->clearAddresses();
   					$mail->clearAttachments();
				}
				$ret[failed] 	= $err;
				$ret[success] 	= $inserted;
				return $ret;
			}else return false;
		}

		static function sendMail($arg = array()){
			if(is_array($arg[recepiens]) && count($arg[recepiens]) > 0){

				$mail = new PHPMailer;
				$news = ($arg[news]) ? true : false;
				$from = ($arg[from]) ? $arg[from] : EMAIL;

				$mail->isSMTP();

                $mail->Host     = SMTP_HOST;
                $mail->Port     = 25;
                $mail->SMTPAuth = true;
                $mail->Username = SMTP_USER;
                $mail->Password = SMTP_PW;
                $mail->setFrom($from, TITLE);
				$mail->addReplyTo($from, TITLE);
				$inserted 			= array();
				$err 				= array();
				$ret 				= array();

				foreach($arg[recepiens] as $r){
					$mail->addAddress($r);
					$mail->WordWrap = 150;                                 // Set word wrap to 50 characters
					$mail->isHTML(true);                                  // Set email format to HTML

					$msg = Helper::emailPatern(array(
						'UZENET' 	=> $arg[msg],
						'ALAIRAS' 	=> $arg[alairas],
						'TEMA_NEV' 	=> $arg[tema],
						'NEWS' 		=> $news,
						'EMAIL' 	=> $r
					));

					$mail->Subject = $arg[sub];
					$mail->msgHtml($msg);
					$mail->AltBody = $mail->html2text($msg);

                    if($arg[file]){
                        $mail->addAttachment($arg[file]);
                    }

					if (!$mail->send()) {
				       	$emsg 	=  "Kiküldés sikertelen: (" . str_replace("@", "&#64;", $r) . ') ' . $mail->ErrorInfo . '<br />';
						$err[] 	= array('mail' => $r, 'msg' => $emsg);
				        break;
				    }else{
				        $inserted[] = $r;
				    }

					$mail->clearAddresses();
   					$mail->clearAttachments();
				}
				$ret[failed] 	= $err;
				$ret[success] 	= $inserted;
				//print_r($ret);
				return $ret;
			}else return false;
		}

		static function getPercent($max, $stat){
			$perc = 0;
			$stat = ( !$stat ) ? 0 : $stat;
			$max = ( !$max ) ? 0 : $max;

			if( $max != 0 && $stat != 0) {
				$perc = $stat / ($max / 100);
			}

			if(!filter_var($perc, FILTER_VALIDATE_INT)){
				$perc = number_format($perc,1,'.','');
			}

			return $perc;
		}

		static function getCookieFilter($filter_prefix = '', $removeKeys = array()){
			if($filter_prefix == '') return false;
			$back = array();
				foreach($_COOKIE as $ck => $cv){
					if(strpos($ck,$filter_prefix) !== false){
						$key = str_replace($filter_prefix.'_','',$ck);
						if(!in_array($key,$removeKeys))
						$back[$key] = $cv;
					}
				}
			return $back;
		}

		static function reload($to = ''){
			$to = ($to == '') ? $_SERVER['HTTP_REFERER'] : $to;
			header('Location: '.$to); exit;
		}

		static function regFilters($str, $reload = false, $arg = array()){
			if($str == '') return false;

			$reg 		= array();
			$precut 	= explode('$', $str);
			$register 	= trim($precut[0]).'_';
            $root       = ($arg[path]) ? '/'.$arg[path] : '/';

			if(strpos($precut[1],'=') === false){
				return false;
			}

			$prestr 	= rtrim($precut[1], '::');
			$keycut 	= explode('::', $prestr);


			foreach($keycut as $k){
				$ck = explode('=', $k);
				$reg[trim($ck[0])] = trim($ck[1]);
			}

			// Reg
			foreach($reg as $rk => $rv){
				setcookie($register.$rk,$rv,0,$root);
			}

			if($reload){
				$reto = $_SERVER['REQUEST_URI'];
				$reto = str_replace(str_replace($precut[0].'$','',$str),'',$reto);
				header('Location: '.$reto);
			}
		}

		static function clearFilters($mode){
			if($mode == '') return false;
			$cleared = true;

			foreach($_COOKIE as $ck => $cv){
				if(strpos($ck,$mode) !== false){
					$cleared = true;
					setcookie($ck,'',time()-60,ADMROOT);
				}
			}

			if($cleared){
				$reto = str_replace('/c/','/',$_SERVER['REQUEST_URI']);
				header('Location: '.$reto);
			}
		}

        static function shuffleAssocArr($list, $arg = array()){
            if (!is_array($list)) return $list;

            $keys = array_keys($list);
            $random = array();
            if(is_array($arg[step]) && count($arg[step]) > 0){
                foreach($arg[step] as $stp){
                    if($stp != '' && array_key_exists($stp,$list)){
                        $random[$stp] = $list[$stp];
                        unset($keys[$stp]);
                    }
                }
            }

            shuffle($keys);

            foreach ($keys as $key) {
                $random[$key] = $list[$key];
            }
            return $random;
        }
	}
?>
