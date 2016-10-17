<? 
namespace PortalManager;

use MailManager\Mailer;
use PortalManager\Template;
use PortalManager\Portal;

/**
 * class Users
 * 
 */
class Users
{
	private $db = null;
	const TABLE_NAME = 'felhasznalok';

	public $user = false;
	private $is_cp = false;

	function __construct( $arg = array() ){
		$this->db = $arg['db'];
		$this->is_cp = $arg['admin'];
		$this->settings = $arg[view]->settings;

		$this->Portal = new Portal( $arg );
		$this->getUser();
	}	
	
	function get( $arg = array() )
	{
		$ret 			= array();
		$kedvezmenyek 	= array();
		$kedvezmeny 	= 0;
		$torzsvasarloi_kedvezmeny = 0;
		$arena_water_card = 0;
		$watercard = array();

		$ret[options] 	= $arg;

		$user_email = ( !$arg['user'] ) ? $this->user : $arg['user'];
		
		if(!$user_email) return false;
		
		$ret[email] = $user_email;
		$ret[data] 	= ($user_email) ? $this->getData($user_email) : false; 

		if( !$ret[data] ) {
			unset($_SESSION['user_email']);
			return false;
		}

		$kedv = $this->getKedvezmeny($ret[data][ID]);
		$torzsvasarloi_kedvezmeny = $kedv[szazalek];

		$ret[szallitasi_adat] = json_decode($ret[data][szallitasi_adatok],true);
		$ret[szamlazasi_adat] = json_decode($ret[data][szamlazasi_adatok],true);

		// Ha hiányzik az adat
		if( (is_null($ret[szallitasi_adat]) || is_null($ret[szamlazasi_adat]) || !$this->validNAVFormat($ret[szallitasi_adat]) || !$this->validNAVFormat($ret[szamlazasi_adat]) ) && !$this->is_cp) 
		{
			if( $_GET['safe'] !='1' ) {
				$miss = '';
				if( is_null($ret[szallitasi_adat]) ) $miss .= 'szallitasi,';
				if( is_null($ret[szamlazasi_adat]) ) $miss .= 'szamlazasi,';
				if( !$this->validNAVFormat($ret[szallitasi_adat]) ) $miss .= 'szallitasirefill,';
				if( !$this->validNAVFormat($ret[szamlazasi_adat]) ) $miss .= 'szamlazasirefill,';
				$miss = rtrim($miss,',');
				\Helper::reload( '/user/beallitasok?safe=1&missed_details='.$miss );
			}
		}

		// WaterCard
		$check_watercard = $this->db->query("SELECT * FROM arena_water_card WHERE felh_id = '{$ret[data][ID]}';");
		$check_watercard_data = $check_watercard->fetch(\PDO::FETCH_ASSOC);

		$watercard[data] = $check_watercard_data;
		$watercard[registered] = ( $check_watercard->rowCount() != 0 ) ? true : false; 
		$watercard[aktiv] = false;
			
		if ( $this->checkWaterCardDiscount( $ret[data][ID] ) ) {
			$arena_water_card = 25; // %
			$ret['arena_water_card_kedvezmeny'] = $arena_water_card;
			$watercard[aktiv] = true;

			$torzsvasarloi_kedvezmeny = 0;
		}
		
		$kedvezmenyek[] = array(
			'nev' 			=> 'Törzsvásárlói kedvezmény',
			'kedvezmeny' 	=> $torzsvasarloi_kedvezmeny,
			'link' 			=> '/p/torzsvasarloi_kedvezmeny'
		);


		$kedvezmenyek[] = array(
			'nev' 			=> 'Arena Water Card',
			'kedvezmeny' 	=> $arena_water_card,
			'link' 			=> '/p/arena_water_card'
		);

		$ret['kedvezmenyek'] = $kedvezmenyek;
		$ret['arena_water_card'] = $watercard;

		$ret['torzsvasarloi_kedvezmeny'] = $torzsvasarloi_kedvezmeny;
		$ret['torzsvasarloi_kedvezmeny_next_price_step'] = $kedv[next_price_step];
		$ret['torzsvasarloi_kedvezmeny_price_steps'] = $kedv[price_steps];

		$ret['kedvezmeny'] = $torzsvasarloi_kedvezmeny + $arena_water_card;
				
		return $ret;
	}
	
	private function validNAVFormat( $arr )
	{
		if(empty($arr['kozterulet_jellege'])) return false;
		if(empty($arr['hazszam'])) return false;

		return true;
	}

	public function checkWaterCardDiscount( $user_id )
	{
		if( !$user_id ) return false;

		$qry = $this->db->query("SELECT arena_water_card FROM felhasznalok WHERE ID = $user_id;");

		if( $qry->rowCount() == 0 ) {
			return false;
		}

		$data = $qry->fetch(\PDO::FETCH_ASSOC);

		if( $data['arena_water_card'] == 0 ) return false;

		return true;
	}

	function resetPassword( $data ){
		$jelszo =  rand(1111111,9999999);
				
		if(!$this->userExists('email',$data['email'])){
			throw new \Exception('Hibás e-mail cím.',1001);		
		}
			
		$this->db->update(self::TABLE_NAME,
			array(
				'jelszo' => \Hash::jelszo($jelszo)
			),
			"email = '".$data['email']."'"
		);

		// Értesítő e-mail az új jelszóról
		$mail = new Mailer( $this->settings['page_title'], $this->settings['email_noreply_address'], $this->settings['mail_sender_mode'] );				
		$mail->add( $data['email'] );	
		$arg = array(
			'settings' 		=> $this->settings,
			'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!',
			'jelszo' 		=> $jelszo
		);
		$mail->setSubject( 'Elkészült új jelszava' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'user_password_reset', $arg ) );			
		$re = $mail->sendMail();
	}

	function getAllKedvezmeny(){
		// Kedvezmény sávok		
		$sv = "SELECT * FROM torzsvasarloi_kedvezmeny ORDER BY ar_from ASC;";	
		
		extract($this->db->q($sv,array('multi' => '1')));	
		
		return $data;
	}

	function getAllElorendelesiKedvezmeny(){
		// Kedvezmény sávok		
		$sv = "SELECT * FROM elorendelesi_kedvezmeny ORDER BY ar_from ASC;";	
		
		extract($this->db->q($sv,array('multi' => '1')));	
		
		return $data;
	}

	private function getKedvezmeny($userID){
		$back = array(
			'szazalek' => 0,
			'prev_paid' => 0,
			'next_price_step' => 999999999,
			'price_steps' => array()
		);
		$kedv = 0;
		$next_step_price = 999999999;
		$price_steps = array();

		if($userID == '') return $back;
		$doneOrderID = $this->db->query("SELECT ID FROM order_allapot WHERE nev = 'Teljesítve';")->fetch(\PDO::FETCH_COLUMN); 
		
		// Korábban rendelt
		$totalOrderPrice = (float) $this->db->query( $oc = "
			SELECT 				sum((o.me * o.egysegAr)) as ar 
			FROM 				`order_termekek` as o 
			WHERE 				o.userID = $userID and  
								datediff(now(),o.hozzaadva) <= 365  and 
								(SELECT allapot FROM orders WHERE ID = o.orderKey) = 4
		")->fetch(\PDO::FETCH_COLUMN);
		$back['prev_paid'] += (int)$totalOrderPrice;
		
		// Hozzáadott érték növelés
		$prev_total = $this->db->query("
				SELECT 				min_ertek 
				FROM 				torzsvasarlo_ertekek 
				WHERE 				email = (SELECT email FROM felhasznalok WHERE ID = {$userID}) and 
									UNIX_TIMESTAMP() < ervenyes
		;")->fetch(\PDO::FETCH_COLUMN);
		
		$back['prev_paid'] += (int)$prev_total;

		if( $prev_total && $prev_total > 0 ) {
			$totalOrderPrice += $prev_total ;
		} 

		// Kosár tartalma
		/* * /
		$cartPrice = $this->db->query( $iqq = "
			SELECT 			sum(IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) * c.me) as cartPrice				 
			FROM 			`shop_kosar` as c 
			LEFT OUTER JOIN shop_termekek as t ON t.ID = c.termekID 
			WHERE 			c.gepID = ".\Helper::getMachineID().";")->fetch(\PDO::FETCH_COLUMN);
		
		if($cartPrice > 0){
			$totalOrderPrice += $cartPrice;
		}
		/* */

		// Kedvezmény sávok		
		$sv = "SELECT ar_from, ar_to, kedvezmeny FROM torzsvasarloi_kedvezmeny ORDER BY ar_from ASC;";	
		
		extract($this->db->q($sv,array('multi' => '1')));

		foreach($data as $d){		

			$from 	= (int)$d[ar_from];	
			$to 	= (int)$d[ar_to];
			$k 		= (float)$d[kedvezmeny];
			
			if($to === 0) $to = 999999999;
			
			if($totalOrderPrice >= $from && $totalOrderPrice <= $to){
				$kedv = $k;
			}

			$price_steps[] = $from;		
		}
		$price_steps[] = 999999999;

		$step = -1;
		foreach ($price_steps as $min ) {		
			if( $step === -1 && $totalOrderPrice < $min ) {
				$step = 0;
				break;
			} else if( $totalOrderPrice < $min ) {
				$step = $step + 1;
				break;
			}
			$step++;
		}

		$next_step_price = $price_steps[$step];

		$back[szazalek] = $kedv;	
		$back[next_price_step] = $next_step_price;	
		$back[price_steps] = $price_steps;	

		return $back;
	}

	private function getPreorderKedvezmeny($userID){
		$kedv = 0;
		if($userID == '') return $kedv;
		$doneOrderID = $this->db->query("SELECT ID FROM order_allapot WHERE nev = 'Teljesítve'")->fetch(\PDO::FETCH_COLUMN); 
		
		// Korábban rendelt
		$totalOrderPrice = (float) $this->db->query("SELECT sum((o.me * o.egysegAr)) as ar FROM `order_termekek` as o WHERE o.userID = $userID and o.szuper_akcios = 0 and datediff(now(),o.hozzaadva) <= 365  and (SELECT allapot FROM orders WHERE ID = o.orderKey) = 4")->fetch(\PDO::FETCH_COLUMN);


		// Kosár tartalma
		$gepid = \Helper::getMachineID();
		if( $gepid == '' || is_null($gepid) || !$gepid ) return 0;
		
		$cartPrice = $this->db->query( $iqq = "SELECT 
				sum(IF(t.egyedi_ar IS NOT NULL, t.egyedi_ar, getTermekAr(t.marka,IF(t.akcios,t.akcios_brutto_ar,t.brutto_ar))) * c.me) as cartPrice				 
			FROM `shop_kosar` as c 
			LEFT OUTER JOIN shop_termekek as t ON t.ID = c.termekID 
			WHERE 
				t.szuper_akcios = 0 and
				c.gepID = ".$gepid.";")->fetch(\PDO::FETCH_COLUMN);
		
		if($cartPrice > 0){
			$totalOrderPrice += $cartPrice;
		}

		// Kedvezmény sávok		
		$sv = "SELECT * FROM elorendelesi_kedvezmeny ORDER BY ar_from ASC;";	
		
		extract($this->db->q($sv,array('multi' => '1')));
		
		foreach($data as $d){
			$from 	= (int)$d[ar_from];	
			$to 	= (int)$d[ar_to];
			$k 		= (float)$d[kedvezmeny];
			
			if($to === 0) $to = 999999999;
			
			if($totalOrderPrice >= $from && $totalOrderPrice <= $to){
				$kedv = $k;
				break;	
			}
			
		}
		
		return $kedv;
	}
	private function getUser(){
		if($_SESSION[user_email]){
			$this->user = $_SESSION[user_email]	;
		}
	}
	function changeUserAdat($userID, $post){
		extract($post);
		if($nev == '') throw new \Exception('A neve nem lehet üress. Kérjük írja be a nevét!');
		
		$this->db->update(self::TABLE_NAME,
			array(
				'nev' => $nev
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changeSzallitasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzallitasi]);
		
		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '' || $phone == '') throw new \Exception('Minden mező kitölétse kötelező!');
		
		$this->db->update(self::TABLE_NAME,
			array(
				'szallitasi_adatok' => json_encode($post,JSON_UNESCAPED_UNICODE)
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}
	
	function changeSzamlazasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzamlazasi]);
		
		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '') throw new \Exception('Minden mező kitölétse kötelező!');
		
		$this->db->update(self::TABLE_NAME,
			array(
				'szamlazasi_adatok' => json_encode($post,JSON_UNESCAPED_UNICODE)
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}
	
	function getOrders($userID, $arg = array()){
		if($userID == '') return false;
		$back = array(
			'done' => array(),
			'progress' => array()
		);
		
		$q = "SELECT 
		o.*,
		oa.nev as allapotNev,
		oa.szin as allapotSzin,
		(SELECT sum(me) FROM `order_termekek` where orderKey = o.ID) as itemNums,
		(SELECT sum(me*egysegAr) FROM `order_termekek` where orderKey = o.ID) as totalPrice
		FROM orders as o 
		LEFT OUTER JOIN order_allapot as oa ON oa.ID = o.allapot
		WHERE o.userID = $userID
		ORDER BY o.allapot ASC, o.idopont ASC ";
		
		$arg[multi] = '1';
		extract($this->db->q($q,$arg));
		
		foreach($data as $d){
			if( $d[kedvezmeny_szazalek] > 0) {
				$d[totalPrice] = $d[totalPrice] / ( $d[kedvezmeny_szazalek] / 100 + 1 ) ;
				\PortalManager\Formater::discountPrice( $d[totalPrice], $d[kedvezmeny_szazalek] );
			}

			if($d[allapotNev] == 'Teljesítve'){
				$back[done][] = $d;
			}else{
				$back[progress][] = $d;
			}
		}
		
		
		return $back;
	}
	
	function changePassword($userID, $post){
		extract($post);

		if($userID == '') throw new \Exception('Hiányzik a felhasználó azonosító! Jelentkezzen be újra.');
		if($old == '') throw new \Exception('Kérjük, adja meg az aktuálisan használt, régi jelszót!');
		if($new == '' || $new2 == '') throw new \Exception('Kérjük, adja meg az új jelszavát!');
		if($new !== $new2) throw new \Exception('A megadott jelszó nem egyezik, írja be újra!');
		
		$jelszo = \Hash::jelszo($old);
		
		$checkOld = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE ID = $userID and jelszo = '$jelszo'");
		if($checkOld->rowCount() == 0){
			throw new \Exception('A megadott régi jelszó hibás. Póbálja meg újra!');	
		}
		
		$this->db->update(self::TABLE_NAME,
			array(
				'jelszo' => \Hash::jelszo($new2)
			),
			"ID = $userID"
		);
	}

	function getData($email){
		if($email == '') return false;
		$q = "SELECT * FROM ".self::TABLE_NAME." WHERE email = '$email'";
		
		extract($this->db->q($q));
		
		return $data;
	}
	
	function login($data){
		$re 	= array();
		
		if(!$this->userExists('email',$data['email'])){
			throw new \Exception('Ezzel az e-mail címmel nem regisztráltak még!',1001);		
		}
		
		if(!$this->validUser($data['email'],$data[pw])){
			if($this->oldUser($data['email'])){
				throw new \Exception('<h3>Weboldalunk megújult, ezért a régi jelszavát nem tudja használni tovább!</h3><br><strong>Jelszóemlékeztető segítségével kérhet új jelszót, amit az e-mail címére elküldünk!<br><a style="color:red;" href="/user/jelszoemlekezteto">ÚJ JELSZÓ MEAGADÁSÁHOZ KATTINTSON IDE!</a></strong>',9000);	
			}else {
				throw new \Exception('Hibás bejelentkezési adatok!',9000);	
			}	
		}
		
		if(!$this->isActivated($data[email])){
			$resendemailtext = '<form method="post" action=""><div class="text-form">Nem kapta meg az aktiváló e-mailt? <button name="activationEmailSendAgain" value="'.$data['email'].'" class="btn btn-sm btn-danger">Aktiváló e-mail újraküldése!</button></div></form>';
			
			throw new \Exception('A fiók még nincs aktiválva! <br>'.$resendemailtext ,1001);		
		}
		
		if(!$this->isEnabled($data[email])){
			throw new \Exception('A fiók felfüggesztésre került!',1001);		
		}
				
		// Refresh
		$this->db->update(self::TABLE_NAME,
			array(
				'utoljara_belepett' => NOW
			),
			"email = '".$data[email]."'"
		);

		$re[email] 	= $data[email];
		$re[pw] 	= base64_encode( $data[pw] );
		$re[remember] = ($data[remember_me] == 'on') ? true : false;
		
		\Session::set('user_email',$data[email]);

		return $re;
	}

	function activate( $activate_arr ){
		$email 	= $activate_arr[0];
		$userID = $activate_arr[1];
		$pwHash = $activate_arr[2];
		
		if($email == '' || $userID == '' || $pwHash == '') throw new \Exception('Hibás azonosító');
		
		$q = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE ID = $userID and email = '$email' and jelszo = '$pwHash'");
		
		if($q->rowCount() == 0) throw new \Exception('Hibás azonosító');
		
		$d = $q->fetch(\PDO::FETCH_ASSOC);
		
		if(!is_null($d[aktivalva]))  throw new \Exception('A fiók már aktiválva van!');
		
		$this->db->update(self::TABLE_NAME,
			array(
				'aktivalva' => NOW
			),
			"ID = $userID"
		);
	}

	function add( $data ){

		// Felhasználó használtság ellenőrzése
		if($this->userExists('email',$data['email'])){

			$is_activated = $this->isActivated( $data['email'] );

			if ( !$is_activated ) {
				$resendemailtext = '<form method="post" action=""><div class="text-form">Nem kapta meg az aktiváló e-mailt? <button name="activationEmailSendAgain" value="'.$data['email'].'" class="btn btn-sm btn-danger">Aktiváló e-mail újraküldése!</button></div></form>';
			}
			
			throw new \Exception('Ezzel az e-mail címmel már regisztráltak! '.$resendemailtext,1002);
		}
		
		// Szállítási és Számlázási adatok JSON kódja
		$szamlazasi_keys = \Helper::getArrayValueByMatch($data,'szam_');
		$szallitasi_keys = \Helper::getArrayValueByMatch($data,'szall_');

		// Arena Water Card
		$water_card = false;		
		if( $data['watercard']['have'] ) {
			if ( $data['watercard']['id'] == '' ) {
				throw new \Exception( "Kérjük, hogy adja meg a A JÖVŐ BAJNOKAINAK / ARENA WATER CARD kártya számát!" );				
			}

			if ( $data['watercard']['egyesulet'] == '' ) {
				throw new \Exception( "Kérjük, hogy adja meg a A JÖVŐ BAJNOKAINAK / ARENA WATER CARD egyesületét!" );				
			}

			$water_card['kartyaszam'] 	= $data['watercard']['id'];
			$water_card['egyesulet'] 	= $data['watercard']['egyesulet'];
		}

		// Felhasználó regisztrálása
		$this->db->insert(self::TABLE_NAME,
			array(
				'email' => trim($data[email]),
				'nev' => trim($data[nev]),
				'jelszo' => \Hash::jelszo($data[pw2]),
				'szamlazasi_adatok' => json_encode($szamlazasi_keys,JSON_UNESCAPED_UNICODE),
				'szallitasi_adatok' => json_encode($szallitasi_keys,JSON_UNESCAPED_UNICODE)
			)
		);

		// Új regisztrált felhasználó ID-ka
		$uid = $this->db->lastInsertId();

		if ( $water_card ) {
			$this->db->insert( 'arena_water_card',
				array(
					'email' 		=> trim($data['email']),
					'felh_id' 		=> $uid,
					'kartya_szam' 	=> $water_card['kartyaszam'],
					'egyesulet' 	=> $water_card['egyesulet']
				)
			);
		}
		
		// Aktiváló e-mail kiküldése
		$this->sendActivationEmail( $data['email'] );
		if ( $water_card ) {
			$this->sendWaterCardAlertEmail( $data, $water_card );
		}
		
		// Feliratkozás 
		if ( $data['subscribe'] ) {
			$this->Portal->feliratkozas( $data['nev'], $data['email'], 'regisztráció' );
		}
		
		return $data;
	}

	public function registerWaterCard( $email, $id, $kartyaszam, $egyesulet )
	{
		if ( $email == '' ) {
			throw new \Exception( "Hiányzik az Ön e-mail címe! Nem tudjuk regisztrálni az ARENA WATER CARD kártyáját!" );				
		}

		if ( $id == '' ) {
			throw new \Exception( "Ismeretlen felhasználó! Nem tudjuk regisztrálni az ARENA WATER CARD kártyáját!" );				
		}

		if ( $kartyaszam == '' ) {
			throw new \Exception( "Kérjük, hogy adja meg a A JÖVŐ BAJNOKAINAK / ARENA WATER CARD kártya számát!" );				
		}

		if ( $egyesulet == '' ) {
			throw new \Exception( "Kérjük, hogy adja meg a A JÖVŐ BAJNOKAINAK / ARENA WATER CARD egyesületét!" );				
		}

		$check = $this->db->query("SELECT 1 FROM arena_water_card WHERE kartya_szam = '$kartyaszam';");

		if( $check->rowCount() != 0 ) {
			throw new \Exception( "A megadott kártyaszámmal rendelkező ARENA WATER CARD kártyát már regisztrálták!" );		
		}

		$this->db->insert( 'arena_water_card',
			array(
				'email' 		=> trim($email),
				'felh_id' 		=> $id,
				'kartya_szam' 	=> $kartyaszam,
				'egyesulet' 	=> $egyesulet
			)
		);

		$this->sendWaterCardAlertEmail( 
			array( 'nev' => $_POST[nev], 'email' => $_POST[watercard][email] ), 
			array( 'egyesulet' => $egyesulet, 'kartyaszam' => $kartyaszam ) 
		);
	}

	public function sendActivationEmail( $email )
	{
		$data = $this->db->query( sprintf(" SELECT * FROM ".self::TABLE_NAME." WHERE email = '%s';", $email) )->fetch(\PDO::FETCH_ASSOC);

		$activateKey = base64_encode(trim($email).'='.$data['ID'].'='.$data['jelszo']);

		// Aktiváló e-mail kiküldése
		$mail = new Mailer( $this->settings['page_title'], $this->settings['email_noreply_address'], $this->settings['mail_sender_mode'] );				
		$mail->add( $email );	
		$arg = array(
			'nev' 			=> trim($data['nev']),
			'settings' 		=> $this->settings,
			'activateKey' 	=> $activateKey,
			'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
		);
		$mail->setSubject( 'Regisztráció aktiválása' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'user_register_activating', $arg ) );			
		$re = $mail->sendMail();
	}

	public function sendWaterCardAlertEmail( $post_data, $water_card )
	{
		// Aktiváló e-mail kiküldése ADMIN RÉSZÉRE
		$mail = new Mailer( $this->settings['page_title'], $this->settings['email_noreply_address'], $this->settings['mail_sender_mode'] );	
		$mail->add( $this->settings['alert_email'] );	
		$arg = array(
			'data' 			=> $post_data,
			'wc' 			=> $water_card,
			'settings' 		=> $this->settings,
			'adminroot' 	=> ADMROOT,
			'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
		);
		$mail->setSubject( 'Értesítés: Jövő Bajnokai kártya / Arena Water Card regisztráció igény' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'admin_user_register_watercard', $arg ) );			
		$re = $mail->sendMail();


		// Értesítő e-mail kiküldése FELHASZNÁLÓ RÉSZÉRE
		$mail = new Mailer( $this->settings['page_title'], $this->settings['email_noreply_address'], $this->settings['mail_sender_mode'] );	
		$mail->add( $post_data[email] );	
		$arg = array(
			'data' 			=> $post_data,
			'wc' 			=> $water_card,
			'settings' 		=> $this->settings,
			'infoMsg' 		=> 'Ezt az üzenetet a rendszer küldte. Kérjük, hogy ne válaszoljon rá!'
		);
		$mail->setSubject( 'Információ: Jövő Bajnokai kártya / Arena Water Card' );
		$mail->setMsg( (new Template( VIEW . 'templates/mail/' ))->get( 'user_register_watercard_info', $arg ) );			
		$re = $mail->sendMail();
	}
	
	function userExists($by = 'email', $val){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE ".$by." = '".$val."'";
		
		$c = $this->db->query($q);
		
		if($c->rowCount() == 0){
			return false;	
		}else{
			return true;	
		}
	}

	function oldUser($email)
	{
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and old_user = 1 and jelszo = 'xxxx';";
		
		$c = $this->db->query($q);
		
		if($c->rowCount() == 0){
			return false;	
		}else{
			return true;	
		}
	}
	
	function isActivated($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and aktivalva IS NOT NULL";
		
		$c = $this->db->query($q);
		
		if($c->rowCount() == 0){
			return false;	
		}else{
			return true;	
		}
	}
	
	function isEnabled($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and engedelyezve = 1";
		
		$c = $this->db->query($q);
		
		if($c->rowCount() == 0){
			return false;	
		}else{
			return true;	
		}
	}
	
	function validUser($email, $password){
		if($email == '' || $password == '') throw new \Exception('Hiányzó adatok. Nem lehet azonosítani a felhasználót!');
		
		$c = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE email = '$email' and jelszo = '".\Hash::jelszo($password)."'");
		
		if($c->rowCount() == 0){
			return false;	
		}else{
			return true;	
		}
	}

	public function getUserList( $arg = array() )
	{
		$q = "
		SELECT 			f.*,
						(SELECT sum(me*egysegAr+o.szallitasi_koltseg-o.kedvezmeny) FROM `order_termekek`as t LEFT OUTER JOIN orders as o ON o.ID = t.orderKey WHERE o.allapot = ".$this->settings['flagkey_orderstatus_done']." and t.userID = f.ID) as totalOrderPrices
		FROM 			felhasznalok as f";
		// WHERE
		$q .= " WHERE f.ID IS NOT NULL";

		if(count($arg[filters]) > 0){
			foreach($arg[filters] as $key => $v){
				switch($key)
				{
					case 'ID':
						$q .= " and f.".$key." = '".$v."' ";
					break;
					case 'nev':
						$q .= " and ".$key." LIKE '".$v."%' ";
					break;
					default: 
						$q .= " and ".$key." = '".$v."' ";
					break;	
				}
				
			}	
		}
		$q .= "
		ORDER BY f.regisztralt DESC
		";
		$arg[multi] = "1";
		extract($this->db->q($q, $arg));	
		
		$B = array();
		foreach($data as $d){
			$d[total_data] = $this->get(array( 'user' => $d['email'] ));
			$B[] = $d; 	
		}
		
		$ret[data] = $B;
		
		return $ret;
	}

	public function __destruct()
	{
		$this->db = null;
		$this->user = false;
	}
}

?>