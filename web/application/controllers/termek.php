<?
use ProductManager\Products;
use PortalManager\Template;

class termek extends Controller{
		function __construct(){
			parent::__construct();
			$title = '';

			$products = new Products( array(
				'db' => $this->db,
				'user' => $this->User->get()
			) );

			$product =  $products->get( Product::getTermekIDFromUrl(), array(
				'b2b' => (defined("B2BLOGGED")) ? true : false
			) );
			$this->out( 'product', $product );


			// Nincs kép a termékről - átirányítás
			if( strpos( $product['profil_kep'] , 'no-product-img' ) !== false ) {
				Helper::reload('/');
			}

			// További ajánlott termékek
			if ( $this->view->product['csoport_kategoria'] )
			{
				// Template
				$temp = new Template( VIEW . 'templates/' );
				$this->out( 'template', $temp );

				$arg = array(
					'except' => array(
						'ID' => Product::getTermekIDFromUrl()
					),
					'limit' => 4,
					'csoport_kategoria' => $product['csoport_kategoria'],
					'b2b' => (defined("B2BLOGGED")) ? true : false
				);

				$related = $products->prepareList( $arg );

				$this->out( 'related', $related );
				$this->out( 'related_list', $related->getList() );
			}

			$title = $product['nev'].' | '.$product['csoport_kategoria'] . ' | Termékek ';

			$this->shop->logTermekView(Product::getTermekIDFromUrl());
			$this->shop->logLastViewedTermek(Product::getTermekIDFromUrl());

			// SEO Információk
			$SEO = null;
			// Site info
			$desc = strip_tags($this->view->product['rovid_leiras']);
			$SEO .= $this->view->addMeta('description',addslashes($desc));
			$keyw = $this->view->product['kulcsszavak'];
			$keyw .= " ".$this->view->product['csoport_kategoria'];
			$SEO .= $this->view->addMeta('keywords',addslashes($keyw));
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('title',addslashes($title));
			$SEO .= $this->view->addOG('description',addslashes($desc));
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url','http://'.$this->view->settings['page_url'].'/'.substr($_SERVER[REQUEST_URI],1));
			$SEO .= $this->view->addOG('image',$product[profil_kep]);
			$SEO .= $this->view->addOG('site_name', $title . ' &mdash; ' . TITLE);

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
