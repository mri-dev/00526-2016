<? 
use ShopManager\Categories;
use ShopManager\Category;
use PortalManager\Pages;

class kategoriak extends Controller {
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Kategóriák / Adminisztráció';
			
			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
			// CREATE
			///////////////////////////////////////////////////////////////////////////////////////		
			$categories = new Categories( array( 'db' => $this->db ) );
			$pages 		= new Pages( false, array( 'db' => $this->db ) );

			// Új kategória
			if( Post::on('addCategory') )
			{
				try {
					$categories->add( $_POST );
					Helper::reload();
				} catch ( Exception $e ) {
					$this->view->err	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}
	
			// Szerkesztés
			if ( $this->view->gets[1] == 'szerkeszt') {
				// Kategória adatok
				$cat_data = new Category( $this->view->gets[2],  array( 'db' => $this->db )  );
				$this->out( 'category', $cat_data );

				// Változások mentése
				if( Post::on('saveCategory') )
				{
					try {
						$categories->edit( $cat_data, $_POST );
						Helper::reload();
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
					}
				}			
			}

			// Törlés
			if ( $this->view->gets[1] == 'torles') {
				// Kategória adatok
				$cat_data = new Category( $this->view->gets[2], array( 'db' => $this->db )  );
				$this->out( 'category_d', $cat_data );

				// Kategória törlése
				if( Post::on('delCategory') )
				{
					try {
						$categories->delete( $cat_data );
						Helper::reload( '/kategoriak' );
					} catch ( Exception $e ) {
						$this->view->err	= true;
						$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
					}
				}			
			}

			$this->out( 'hashkeyed_pages', $pages->getTree() );


			// LOAD
			////////////////////////////////////////////////////////////////////////////////////////			
			$cat_tree 	= $categories->getTree();
			// Kategoriák
			$this->out( 'categories', $cat_tree );
						
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
		
		function parameterek(){
			// Termék kategóriák
			$this->view->termekkategoriak = $this->AdminUser->getTermekKategoriak();
			
			if(Post::on('addParameter')){
				try{
					$this->AdminUser->addParameterOnTermekKategoria($_POST);
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 			= true;
					$this->view->bmsg[termkat] 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}
			
			if(Post::on('delParamId')){
				$this->model->db->query("DELETE FROM shop_termek_kategoria_parameter WHERE ID = {$_POST[delParamId]}");
				Helper::reload('/kategoriak/parameterek/');
			}
			
			// Termék kategória paraméterek lista
			$this->view->listOfTermekKatParam = $this->AdminUser->listTermekKategoriaParameterek();
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>