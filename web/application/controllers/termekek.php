<?
use ShopManager\Category;
use ShopManager\Categories;
use ProductManager\Products;
use PortalManager\Template;
use PortalManager\Pagination;

class termekek extends Controller {
		function __construct(){
			parent::__construct();
			$title = 'Termékek';

			// Template
			$temp = new Template( VIEW . 'templates/' );
			$this->out( 'template', $temp );

			// Kategória adatok
			$cat = new Category(Product::getTermekIDFromUrl(), array( 'db' => $this->db ));
			if( is_null($cat->getId()) ) {
				Helper::reload('/');
			}
			$this->out( 'category', $cat );

			// Kategória szülő almenüi
			$categories = new Categories( array( 'db' => $this->db ) );

			$parent_id = ($cat->getParentId()) ?: $cat->getId();
			$parent_list = $categories->getTree( $parent_id );
			$this->out( 'parent_menu', $parent_list );

			// Szülők
			$parent_set = array_reverse( $categories->getCategoryParentRow( $cat->getId(), 'neve', 0 ) );

			$i = 0;
			foreach( $parent_set as $parent_i ) {
				$i++;

				$after = '';

				if( $i == 1 ) {
					$after = ' termékek';
				} else if( $i == 2 )  {
					$after = ' kategória';
				}

				$title = $parent_i.$after. ' | '.$title;
			}

			// Termékek
			$filters = array();
			$order = array();

			if( $_GET['order']) {
				$xord = explode("_",$_GET['order']);
				$order['by'] 	= $xord[0];
				$order['how'] 	= $xord[1];
			}

			$arg = array(
				'b2b' 		=> (defined('B2BLOGGED')) ? true : false,
				'filters' => $filters,
				'in_cat' 	=> $cat->getId(),
				'meret' 	=> $_GET['meret'],
				'order' 	=> $order,
				'limit' 	=> 40,
				'page' 		=> Helper::currentPageNum()
			);
			$products = (new Products( array(
				'db' => $this->db,
				'user' => $this->User->get()
			) ))->prepareList( $arg );
			$this->out( 'products', $products );
			$this->out( 'product_list', $products->getList() );

			$get = $_GET;
			unset($get['tag']);
			$get = http_build_query($get);
			$this->out( 'cget', $get );
			$this->out( 'navigator', (new Pagination(array(
				'class' => 'pagination pagination-sm center',
				'current' => $products->getCurrentPage(),
				'max' => $products->getMaxPage(),
				'root' => '/'.__CLASS__.'/'.$this->view->gets[1].($this->view->gets[2] ? '/'.$this->view->gets[2] : '/-'),
				'after' => ( $get ) ? '?'.$get : '',
				'item_limit' => 12
			)))->render() );

			// Log AV
			/* */
			$this->shop->logKategoriaView(
				Product::getTermekIDFromUrl()
			);
			/* */

			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','ARENA Sportruházat és fürdőruha webáruház - Úszósapka, úszószemüveg, fürdőruhák. Akciók, törzsvásárlói kedvezmény, 10.000 Ft feletti vásárláskor AJÁNDÉK termék. Úszódresszek, bikinik, fürdőnadrág és úszónadrág. Szabadidőruha, női és férfi póló, köntös, sporttáska, papucs és egyéb sportruházati kiegészítők nagy választékban.');
			$SEO .= $this->view->addMeta('keywords','úszósapka, úszószemüveg, fürdőruha, sportruházat, arena, fürdőnadrág, úszónadrág');
			$SEO .= $this->view->addMeta('revisit-after','3 days');

			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',substr(DOMAIN,0,-1).$_SERVER['REQUEST_URI']);
			$SEO .= $this->view->addOG('image',substr(IMG,0).'logo_200x_black.png');
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
