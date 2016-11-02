<?
use B2B\B2BAuth;

class b2b extends Controller{
		const SESSION_URL_TIMEOUT_SEC = 60;
		function __construct(){
			parent::__construct();

			if(isset($_GET['validAuth']))
			{
				$msg = "<h3 style='margin: 0 0 20px 0;'>Sikeres azonosítás, bejelentkező email elküldve!</h3>E-mail címére megküldtük a bejelentkező URL-t, amivel beléphet a rendszerbe. A bejelenetkező link ".self::SESSION_URL_TIMEOUT_SEC." perc után elévűl.";
				$this->out('rmsg', Helper::makeAlertMsg('pSuccess', $msg));
				unset($msg);
			}

			$auth = new B2BAuth($this->db);

			if(isset($_GET['validateAuthSession']))
			{
				try {
					$authed = $auth->loginBySession($_GET['validateAuthSession']);
					if ($authed) {
						Helper::reload('/b2b/');
					}
				} catch (Exception $e) {
					$this->view->rmsg = Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if (Post::on('authB2B'))
			{
				try {
					$auth_user = $auth->login($_POST['email'], $_POST['pw']);
					Helper::reload('/b2b/?validAuth=1');
				} catch (Exception $e) {
					$this->view->rmsg = Helper::makeAlertMsg('pError', $e->getMessage());
				}
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

		public function logout()
		{
			unset($_SESSION['b2buserid']);
			Helper::reload('/b2b/');
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
