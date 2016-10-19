<?
use B2B\B2BUsers;
use B2B\B2BUser;
use PortalManager\Pagination;

class b2b extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'B2B';

			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

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

    public function users()
    {
			$users = new B2BUsers( $this->db );


			switch($this->gets[2])
			{
				// Felhaszálók listázása oldal
				default:
					$users->setPageLimit(25);

					// B2B Felhaszálók listájának lekérdezése
					$this->out('users', $users->get(array(
					)));

					// Lapozó
					$this->view->navigator = (new Pagination(array(
						'class' => 'pagination pagination-sm center',
						'current' => $users->listCurrentPage(),
						'max' =>  $users->listMaxPage(),
						'root' => '/'.__CLASS__.'/'.__FUNCTION__,
						'after' => ( $get ) ? '?'.$get : '',
						'item_limit' => 12
					)))->render();
				break;

				// Felhasználók létrehozása oldal
				case 'create':

				break;

				// Felhasználó szerkesztés oldal
				case 'edit':
					$user = (new B2BUser($this->db))->get( $this->gets[3] );
					$this->out('u', $user );
				break;

				// Felhasználó törlés oldal
				case 'delete':

				break;
			}

			// B2B Felhasználók osztály
			$this->out('b2busers', $users);
    }

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
