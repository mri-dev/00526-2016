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


			if(isset($_GET['rmsg'])) {
				$this->out('rmsg', Helper::makeAlertMsg($_GET['t'], $_GET['rmsg']));
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
		}

    public function users()
    {
			$users = new B2BUsers( $this->db );

			switch($this->gets[2])
			{
				// Felhaszálók listázása oldal
				default:

					if(Post::on('filterList')){
						$filtered = false;

						if($_POST[ID] != ''){
							setcookie('filter_ID',$_POST[ID],time()+60*24,'/'.$this->view->gets[0]);
							$filtered = true;
						}else{
							setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
						}
						if($_POST[nev] != ''){
							setcookie('filter_nev',$_POST[nev],time()+60*24,'/'.$this->view->gets[0]);
							$filtered = true;
						}else{
							setcookie('filter_nev','',time()-100,'/'.$this->view->gets[0]);
						}
						if($_POST[email] != ''){
							setcookie('filter_email',$_POST[email],time()+60*24,'/'.$this->view->gets[0]);
							$filtered = true;
						}else{
							setcookie('filter_email','',time()-100,'/'.$this->view->gets[0]);
						}

						if($filtered){
							setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
						}else{
							setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
						}
						Helper::reload();
					}

					$users->setPageLimit(25);

					// B2B Felhaszálók listájának lekérdezése
					$arg = array();
					$filters = Helper::getCookieFilter('filter',array('filtered'));
					$arg[filters] = $filters;

					$this->out('users', $users->get($arg));

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

				$user = new B2BUser($this->db);
				if (Post::on('createUser')) {
					unset($_POST['createUser']);
					try {
						$cruid = $user->create($_POST);
						Helper::reload('/b2b/users/edit/'.$cruid.'?rmsg=Felhasználó létrehozva&t=pSuccess');
					} catch (\Exceptions\FormException $e) {
						$this->view->err	= $e->getErrorData();
						$emsg_af = '';
						if($this->view->err['miss_count'] != 0) {
							$emsg_af .= " | Hiányzó mezők: ".$this->view->err['miss_count']." db";
						}
						$this->view->rmsg = Helper::makeAlertMsg('pError', $e->getMessage().$emsg_af);
					}
				}

				break;

				// Felhasználó szerkesztés oldal
				case 'edit':
					$user = (new B2BUser($this->db))->get( $this->gets[3] );

					if(!$user->hasUser()){
						Helper::reload('/b2b/users/');
					}

					if (Post::on('saveUser')) {
						unset($_POST['saveUser']);
						try {
							$user->save($_POST);
							Helper::reload('?rmsg=Változások sikeresen mentve&t=pSuccess');
						} catch (Exception $e) {
							$this->view->err	= true;
							$this->view->rmsg = Helper::makeAlertMsg('pError', $e->getMessage());
						}

					}

					$this->out('u', $user );
				break;

				// Felhasználó törlés oldal
				case 'delete':

				break;

				// Fiók aktiválás
				case 'activate':
					$user = (new B2BUser($this->db))->get( $this->gets[3] );
					$user->activate();
					Helper::reload('/b2b/users/edit/'.$user->ID());
				break;

				// Fiók felfüggesztés
				case 'deactivate':
					$user = (new B2BUser($this->db))->get( $this->gets[3] );
					$user->deactivate();
					Helper::reload('/b2b/users/edit/'.$user->ID());
				break;

				// Szűrőfeltételek törlése
				case 'clearfilters':
					setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
					setcookie('filter_nev','',time()-100,'/'.$this->view->gets[0]);
					setcookie('filter_email','',time()-100,'/'.$this->view->gets[0]);
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
					Helper::reload('/'.$this->view->gets[0].'/users');
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
