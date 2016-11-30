<? 
use Applications\Simple;
use Applications\PayU;

class order extends Controller{
		function __construct(){	
			parent::__construct();
			$title = 'Megrendelés adatlapja';
						
			if($this->view->gets[1] == ''){
				Helper::reload('/');
			}

			// PayPal befizetés logolás
			if($this->view->gets[2] == 'paid_via_paypal'){
				if(!$this->shop->orderAlreadyPaidViaPayPal($this->view->gets[1])){
					$this->shop->setOrderPaidByPayPal($this->view->gets[1]);
					Helper::reload('/'.__CLASS__.'/'.$this->view->gets[1]);
				}else{
					Helper::reload('/'.__CLASS__.'/'.$this->view->gets[1]);
				}
			}
			
			$this->view->orderAllapot = $this->shop->getMegrendelesAllapotok();
			$this->view->szallitas 	= $this->shop->getSzallitasiModok();
			$this->view->fizetes 	= $this->shop->getFizetesiModok();
			
			$this->view->order = $this->shop->getOrderData($this->view->gets[1]);
			$this->view->order_user = $this->User->get( array( 'user' => $this->view->order[email] ) );
			
			if(empty($this->view->order[items])){
				Helper::reload('/');
			}

			/** Simple FIZETÉS */
			$order_id = $this->view->order['azonosito'];

			if( $order_id == '' ){
				Helper::reload( '/user' );
			}

			/*echo '<pre>';
			print_r($this->view->order );
			echo '</pre>';*/

			$this->view->order['szallitas_adat'] 		= json_decode($this->view->order['szallitasi_keys'], true);
			$this->view->order['szamlazas_adat'] 		= json_decode($this->view->order['szamlazasi_keys'], true);


			if( true ) {
				$this->pay = (new Simple())
					->setMerchant( 	'HUF', 	$this->view->settings['payu_merchant'])
					->setSecretKey( 'HUF',	$this->view->settings['payu_secret'] )
					->setCurrency( 	'HUF' )
					->setOrderId( $order_id )
					->setData( $this->view->order );
			} 

			if( false ) {
				$this->pay = (new PayU())
					->setMerchant( 	'HUF', 	$this->view->settings['payu_merchant'])
					->setSecretKey( 'HUF',	$this->view->settings['payu_secret'] )
					->setCurrency( 	'HUF' )
					->setOrderId( $order_id )
					->setData( $this->view->order );
			}

			if ( $this->view->order['szallitasi_koltseg'] > 0 ) {
				$this->pay->setTransportPrice( $this->view->order['szallitasi_koltseg'] );
			}

			// Kedvezmény (%) kiszámítása
			$discount = 0;
			if ( !empty($this->view->order['items']) ) {
				$total_ar = 0;
				foreach ($this->view->order['items'] as $ai ) {
					$total_Ar += $ai['subAr'];
				}
			}	

			$this->pay->prepare();

			$this->out( 'payu_btn', $this->pay->getPayButton() );
												
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
		
			
			parent::$pageTitle = $title;
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>