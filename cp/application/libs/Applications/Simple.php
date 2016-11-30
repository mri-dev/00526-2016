<?
namespace Applications;

class Simple
{
	private $config 			= array();
	private $live 				= null;
	private $currency 			= 'HUF';
	private $order_id 			= null;
	private $datas 				= null;
	private $utanvet 			= false;
	private $discount 			= 0;
	private $transport_price 	= 0;

	private $pay_form = null;

	function __construct() {
		require_once "simplesdk/config.php";
		require_once "simplesdk/PayUPayment.class.php";
		require_once "simplesdk/PayUPaymentExtra.class.php";

		$this->config = $config;

		return $this;
	}

	public function getCurrency()
	{
		return $this->currency;
	}

	public function getBackref()
	{
		return new \PayUBackRef( $this->config );
	}

	public function getIPN()
	{
		return new \PayUIpn( $this->config );
	}

	public function getIDN()
	{
		return new \PayUIdn( $this->config );
	}

	public function prepare()
	{
		$this->live = new \PayULiveUpdate($this->config);

		/**
		 * Oreder global data (most cases no need to modify)
		 */
		$this->live->setField("PRICES_CURRENCY", 	$this->currency);
		$this->live->setField("ORDER_DATE", 		$this->config['ORDER_DATE']);
		$this->live->setField("BACK_REF", 			$this->config['BACK_REF']);
		$this->live->setField("TIMEOUT_URL", 		$this->config['TIMEOUT_URL']);
		$this->live->setField("ORDER_TIMEOUT", 		$this->config['ORDER_TIMEOUT']);

		/**
		 * Payment method
		 */
		//only the given method
		if ( $this->config['METHOD'] != '' ) {
			$this->live->setField("PAY_METHOD", $this->config['METHOD']);
			$this->live->setField("AUTOMODE", 1);
		}
		//select payment method on payment page
		elseif ( $this->config['METHOD']=='' ) {
			$this->live->setField("PAY_METHOD", '');
			$this->live->setField("AUTOMODE", 	0);
		}

		if( $this->utanvet ){
			// Utánvételes fizetás
			$this->live->setField("PAY_METHOD", "CASH");
		}


		/**
		 * Order global data (need to fill by order data)
		 */
		$this->live->setField("ORDER_REF", $this->order_id);

		$this->live->setField("ORDER_SHIPPING", 	$this->transport_price);
		$this->live->setField("LANGUAGE", 			$this->config['LANGUAGE']);
		$this->live->setField("ORDER_PRICE_TYPE", 	"GROSS");			// [ GROSS | NET ]


		$total = 0;
		foreach( $this->datas['items'] as $d ):
			$price = round($d['egysegAr']);

			if ($this->datas['b2b'] == 1) {
				$price = round($price * AFA);
			}

			$info = 'Cikkszám: '.$d['cikkszam'].' ';
			if ( $d['meret'] ) {
				$info .= 'Méret: '.$d['meret'].' ';
			}

			if ( $d['szin'] ) {
				$info .= 'Szín: '.$d['szin'].' ';
			}

			$this->live->addProduct(array(
				'name' 	=> $d[nev],				//product name [ string ]
				'code' 	=> $d[raktar_variantid],//merchant systemwide unique product ID [ string ]
				'info' 	=> $info,				//product description [ string ]
				'price' => $price, 				//product price [ HUF: integer | EUR, USD decimal 0.00 ]
				'vat' 	=> 0,					//product tax rate [ in case of gross price: 0 ] (percent)
				'qty' 	=> $d[me]				//product quantity [ integer ]
			));
			$total += $price * $d[me];
		endforeach;

		// Százalékos kedvezmény
		if( $this->discount != 0 ) {
			$this->live->setField("DISCOUNT", $this->discount);
		}

		/**
		 * Billing data
		 */
		$nev = explode(" ", $this->datas[szamlazas_adat][nev] );
		$this->live->setField("BILL_FNAME", $nev[0]);
		$this->live->setField("BILL_LNAME", $nev[1]);
		$this->live->setField("BILL_EMAIL", $this->datas[email]);
		$this->live->setField("BILL_PHONE", $this->datas[szallitas_adat][phone]);
		/*if($this->datas[user_data][szam_company] != ''):
			$this->live->setField("BILL_COMPANY", $this->datas[user_data][szam_company]);			// optional
			$this->live->setField("BILL_FISCALCODE", $this->datas[user_data][szam_vat]);					// optional

		endif;*/
	//
		$this->live->setField("BILL_COUNTRYCODE", "HU");
		$this->live->setField("BILL_STATE", $this->datas[szamlazas_adat][state]);
		$this->live->setField("BILL_CITY", $this->datas[szamlazas_adat][city]);
		$this->live->setField("BILL_ADDRESS", $this->datas[szamlazas_adat][uhsz] );
		//$this->live->setField("BILL_ADDRESS2", "Second line address");		// optional
		$this->live->setField("BILL_ZIPCODE", $this->datas[szamlazas_adat][irsz]);

		/**
		 * Delivery data
		 */
		$this->live->setField("DELIVERY_FNAME", $nev[0]);
		$this->live->setField("DELIVERY_LNAME", $nev[1]);
		$this->live->setField("DELIVERY_EMAIL", $this->datas[email]);
		$this->live->setField("DELIVERY_PHONE", $this->datas[szallitas_adat][phone]);
		$this->live->setField("DELIVERY_COUNTRYCODE", "HU");
		$this->live->setField("DELIVERY_STATE", $this->datas[szallitas_adat][state]);
		$this->live->setField("DELIVERY_CITY", $this->datas[szallitas_adat][city]);
		$this->live->setField("DELIVERY_ADDRESS",  $this->datas[szallitas_adat][uhsz] );
		//$this->live->setField("DELIVERY_ADDRESS2", "Second line address");	// optional
		$this->live->setField("DELIVERY_ZIPCODE", $this->datas[szallitas_adat][irsz]);

		$this->live->logger 	= $this->config['LOGGER'];
		$this->live->log_path 	= $this->config['LOG_PATH'];


		$display = $this->live->createHtmlForm('PayUForm', 'button', "" );	// format: link, button, auto (auto is redirects to payment page immediately )
		//print_r($this->live->getMissing());
		$this->pay_form = $display;
		return $this;
	}

	public function getPayButton()
	{
		return $this->pay_form;
	}

	/** SETTERS **/
	public function setTransportPrice( $price )
	{
		$this->transport_price = $price;
	}
	public function setDiscount( $price )
	{
		$this->discount = $price;
	}
	public function setUtanvet( $flag )
	{
		$this->utanvet = $flag;
	}
	public function setOrderId($id)
	{
		$this->order_id = $id;
		return $this;
	}
	public function setData($data)
	{
		$this->datas = $data;
		return $this;
	}
	public function setMerchant($currency, $merchant )
	{
		$this->config[$currency.'_MERCHANT'] = $merchant;
		return $this;
	}

	public function setSecretKey($currency, $key )
	{
		$this->config[$currency.'_SECRET_KEY'] = $key;
		return $this;
	}

	public function setPayMethod( $method )
	{
		$this->config['METHOD'] = $method;
		return $this;
	}

	public function setCurrency($currency)
	{
		$this->currency = $currency;
		$modifyConfig 	= new \PayUModifyConfig($this->config);
		$this->config	= $modifyConfig->merchantByCurrency($currency);
		return $this;
	}

}
?>
