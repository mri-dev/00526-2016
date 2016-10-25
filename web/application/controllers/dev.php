<? 
use Applications\Simple;

class dev extends Controller{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'DEV';
			
			//setcookie('dev','1',time()+9999,'/');
			
			if( $_GET['xxx'] != 'xxx' ) { exit; }
			
			$this->db->query("SELECT * FROM tables2"); 
			
			/*			
			$this->view->order = array(
				"ID" => 27,
				"azonosito" => "ARENASIMPLETEST0000001",
				"nev" => "MRI",
				"userID" => 9999,
				"gepID" => 2045230988,
				"email" => "info@mri-dev.com",
				"accessKey" => "8a1cb06ca3616d4abce132ea2735869a",
				"szallitasiModID" => 1,
				"fizetesiModID" => 5,
				"kedvezmeny" => 0,
				"kedvezmeny_szazalek" => "10",
				"szallitasi_koltseg" => "1270",
				"szamlazasi_keys" => '{"nev":"Nemzeti Úszóegyesület Kft.","uhsz":"Nagyhegyesi u 88-92","state":"Budapest","city":"Budapest","irsz":"1111"}',
				"szallitasi_keys" => '{"nev":"MI SS","uhsz":"Kiss u. 154","state":"Hajdú-Bihar","city":"Debrecen","irsz":"5555","phone":"+36301234567"}',
				"allapot" => 1,
				"pickpackpont_uzlet_kod" => NULL,
				"postapont" => NULL,
				"elorendeles" => 0,
				"paypal_fizetve" => 0,
				"payu_fizetve" => 0,
				"payu_teljesitve" => 0,
				"comment" => "",
				"idopont" => "2015-10-30 15:34:10",
				"items" => array
					(
						"0" => array
							(
								"ID" => 9999,
								"orderKey" => 27,
								"gepID" => "2045230988",
								"userID" => 9999,
								"email" => "info@mri-dev.com",
								"termekID" => "40363",
								"me" => 1,
								"egysegAr" => 100,
								"allapotID" => 1,
								"hozzaadva" => "2015-10-30 15:34:10",
								"nev" => "Cucc 1",
								"subAr" => 100,
								"profil_kep" => "src/products/all/92411_54_1.jpg",
								"meret" => "",
								"szin" => "füst / piros",
								"cikkszam" => "11808-120069",
								"raktar_variantid" => "120069",
								"url" => "http://www.arena.wwwsoft.tk/termek/aquaforce_-40363",
								"ar" => 100,
								"allapotNev" => "Feldolgozás alatt",
								"allapotSzin" => "#c95a5a",
								"kedvezmeny_szazalek" => 0
							),
						"1" => array
							(
								"ID" => 99991,
								"orderKey" => 27,
								"gepID" => "2045230988",
								"userID" => 9999,
								"email" => "info@mri-dev.com",
								"termekID" => "40363",
								"me" => 2,
								"egysegAr" => 200,
								"allapotID" => 1,
								"hozzaadva" => "2015-10-30 15:34:10",
								"nev" => "Cucc 2",
								"subAr" => 400,
								"profil_kep" => "src/products/all/92411_54_1.jpg",
								"meret" => "",
								"szin" => "füst / piros",
								"cikkszam" => "11808-120069",
								"raktar_variantid" => "120069",
								"url" => "http://www.arena.wwwsoft.tk/termek/aquaforce_-40363",
								"ar" => 200,
								"allapotNev" => "Feldolgozás alatt",
								"allapotSzin" => "#c95a5a",
								"kedvezmeny_szazalek" => 0
							)

					)
			);
			$order_id = $this->view->order['azonosito'];			
			
			$this->pay = (new Simple())
				->setMerchant( 	'HUF', 	$this->view->settings['payu_merchant'])
				->setSecretKey( 'HUF',	$this->view->settings['payu_secret'] )
				->setCurrency( 	'HUF' )
				->setOrderId( $order_id )
				->setData( $this->view->order );
				
			$this->pay->setTransportPrice( 990 );
			$this->pay->prepare();

			$this->out( 'pay_btn', $this->pay->getPayButton() );
			
			echo $this->pay->getPayButton();
			*/
			
			
			
			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);
			
			$this->view->SEOSERVICE = $SEO;
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>