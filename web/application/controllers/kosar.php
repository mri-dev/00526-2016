<?
use ShopManager\OrderException;
use Applications\PayU;
use Applications\Simple;

class kosar extends Controller{

		private $preorder_index	= 'cart_preorder';
		private $preorder_flag 	= false;

		function __construct(){
			parent::__construct();
			$title = '';

			if(Post::on('clearCart')){
				$this->shop->clearCart(Helper::getMachineID());
				Helper::reload('/kosar/');
			}

			$this->view->canOrder 			= false;
			$this->view->orderMustFillStep 	= array();

			$this->view->kosar 		= $this->shop->cartInfo(Helper::getMachineID(), array(
				'b2b' => (defined('B2BLOGGED')) ? true : false
			));
			$this->view->szallitas 	= $this->shop->getSzallitasiModok();
			$this->view->fizetes 	= $this->shop->getFizetesiModok();

			$this->view->storedString[] = Helper::getbackPOSTData('order_step_1');
			$this->view->storedString[] = Helper::getbackPOSTData('order_step_2');
			$this->view->storedString[] = Helper::getbackPOSTData('order_step_3');
			$this->view->storedString[] = Helper::getbackPOSTData('order_step_4');

			//$this->view->ppp->data 		= $this->ppp->getPointData($this->view->storedString[2][ppp_uzlet]);

			if($this->view->gets[1] == '5'){
				Helper::reload('/kosar/done/'.$_COOKIE[lastOrderedKey]);
			}

			if(
				!empty($this->view->storedString[0]) &&
				!empty($this->view->storedString[1]) &&
				!empty($this->view->storedString[2]) &&
				!empty($this->view->storedString[3])
			){
				$this->view->canOrder = true;
			}else{
				if(empty($this->view->storedString[0])) $this->view->orderMustFillStep[] = 0;
				if(empty($this->view->storedString[1])) $this->view->orderMustFillStep[] = 1;
				if(empty($this->view->storedString[2])) $this->view->orderMustFillStep[] = 2;
				if(empty($this->view->storedString[3])) $this->view->orderMustFillStep[] = 3;
			}

			// PickPackPont szállítás esetén, ha nincs kiválasztva a PPP, akkor nem lehet megrendelni
			if( $this->view->storedString[2][atvetel] == $this->view->settings['flagkey_pickpacktransfer_id'] &&
				$this->view->storedString[2][ppp_uzlet_n] == ''
			){
				$this->view->canOrder = false;
			}

			if (defined('B2BLOGGED')) {
				$min_price_order = $this->view->settings[b2b_order_min_price];
			} else {
				$min_price_order = $this->view->settings[order_min_price];
			}

			if( $this->view->kosar[totalPrice] < $min_price_order ) {
				$this->view->canOrder = false;
				$this->view->not_reached_min_price_text = 'Minimális vásárlási érték <strong>'.Helper::cashFormat($min_price_order).' Ft</strong>! A kosarában található termékek összesített értéke nem haladja meg ezt az értéket!';
			}

			// PostaPont szállítás esetén, ha nincs kiválasztva a PP, akkor nem lehet megrendelni
			/*if($this->view->storedString[2][atvetel] == '5' && $this->view->storedString[2][pp_selected] == ''){
				$this->view->canOrder = false;
			}*/

			if( !empty($this->view->kosar[overStock]) ) {
				$this->view->canOrder = false;
				$this->view->not_reached_min_price_text = 'Az Ön kosarában '.count($this->view->kosar[overStock]).' db olyan tétel van, ahol többet szeretne vásárolni, mint amennyi jelenleg készleten van! Csökkentse a mennyiséget a maximálisan megrendelhető mennyiségre.';
			}

			if(Post::on('orderState')){
				try{
					$step = $this->shop->doOrder($_POST, array( 'user' => $this->view->user ));
					Helper::reload('/kosar/'.$step.'#step');
				}catch(OrderException $e){
					$this->view->orderExc = $e->getErrorData();
					$this->out( 'msg', \Helper::makeAlertMsg('pError', $this->view->orderExc['msg']) );
				}
			}

			$this->view->orderStep = (!$_COOKIE[\ShopManager\Shop::ORDER_COOKIE_KEY_STEP]) ? 0 : (int)$_COOKIE[\ShopManager\Shop::ORDER_COOKIE_KEY_STEP];


			if($_COOKIE[\ShopManager\Shop::ORDER_COOKIE_KEY_STEP] && $this->view->gets[1] > $this->view->orderStep){
				Helper::reload('/kosar/'.$this->view->orderStep);
			}

			if($this->view->orderStep == 0 && $this->view->gets[1] != ''){
				Helper::reload('/kosar/');
			}


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

		function done(){
			$this->view->accessKey 	= $this->view->gets[2];
			$this->view->orderAllapot 	= $this->shop->getMegrendelesAllapotok();
			$this->view->szallitas 		= $this->shop->getSzallitasiModok();
			$this->view->fizetes 		= $this->shop->getFizetesiModok();

			$this->view->orderInfo = $this->shop->getOrderData($this->view->accessKey);
			$this->view->order_user 	= $this->User->get( array( 'user' => $this->view->orderInfo[email] ) );


			/** PAYU FIZETÉS */
			$order_id = $this->view->orderInfo['azonosito'];

			if( $order_id == '' ){
				Helper::reload( '/user' );
			}

			$this->view->orderInfo['szallitas_adat'] 		= json_decode($this->view->orderInfo['szallitasi_keys'], true);
			$this->view->orderInfo['szamlazas_adat'] 		= json_decode($this->view->orderInfo['szamlazasi_keys'], true);

			$this->payu = (new Simple())
				->setMerchant( 	'HUF', 	$this->view->settings['payu_merchant'])
				->setSecretKey( 'HUF',	$this->view->settings['payu_secret'] )
				->setCurrency( 	'HUF' )
				->setOrderId( $order_id )
				->setData( $this->view->orderInfo );

			if ( $this->view->orderInfo['szallitasi_koltseg'] > 0 ) {
				$this->payu->setTransportPrice( $this->view->orderInfo['szallitasi_koltseg'] );
			}

			// Kedvezmény (%) kiszámítása
			$discount = 0;
			if ( !empty($this->view->orderInfo['items']) ) {
				$total_ar = 0;
				foreach ($this->view->orderInfo['items'] as $ai ) {
					$total_Ar += $ai['subAr'];
				}
			}

			$this->payu->prepare();

			$this->out( 'payu_btn', $this->payu->getPayButton() );
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
