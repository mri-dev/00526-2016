<?
class user extends Controller{
		function __construct(){
			parent::__construct( array( 'admin' => false ) );
			$title = 'Fiókom';

			if( !$this->view->user &&
				$this->view->gets[1] != 'activate' &&
				$this->view->gets[1] != 'belepes' &&
				$this->view->gets[1] != 'regisztracio' &&
				$this->view->gets[1] != 'jelszoemlekezteto'
			){
				Helper::reload('/user/belepes');
			}

			// B2B átirányítás, ha be van lépve.
			if (defined('B2BLOGGED')) {
				$to = str_replace("/user", "/b2b", $_SERVER['REQUEST_URI']);
				Helper::reload($to);
			}

			if($this->view->gets[1] == ''){
				//Helper::reload('/'.$this->view->gets[0].'/megrendelesek');
			}

			// Aktiváló újraküldése
			if ( Post::on('activationEmailSendAgain') ) {
				try{
					$re = $this->User->sendActivationEmail( $_POST['activationEmailSendAgain'] );
					Helper::reload('/user/belepes?msg=Aktiváló e-mailt elküldök az e-mail címére!');
				}catch(Exception $e){
					$err = $e->getCode();
					$this->out( 'err', $err );
					$this->view->msg = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}

			if ( $_GET['msg'] ) {
				$this->out( 'msg', Helper::makeAlertMsg('pSuccess',$_GET['msg']) );
			}

			// Megrendelések
			$arg = array();
			$this->view->orders = $this->User->getOrders($this->view->user[data][ID],$arg);

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

		function activate(){
			$key = base64_decode($this->view->gets[2]);
			$key = explode('=',$key);

			try{
				$this->User->activate($key);
			}catch(Exception $e){
				$this->out( 'msg', $e->getMessage() );
				$this->out( 'err', true );
				//Helper::reload('/');
			}
		}

		function beallitasok(){
			if ( !$this->view->user ) {
				Helper::reload('/user/belepes');
			}

			// Watercard reg
			if(Post::on('addWatercard')){
				try{
					$re = $this->User->registerWaterCard( $_POST[watercard][email], $_POST[watercard][userid], $_POST[watercard][id], $_POST[watercard][egyesulet] );
					$this->view->msg['alapadat'] = Helper::makeAlertMsg('pSuccess',$re);
					Helper::reload('/user/beallitasok');
				}catch(Exception $e){
					$this->view->err = true;
					$this->view->msg['alapadat'] = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}

			// Alapadatok cseréje
			if(Post::on('saveDefault')){
				try{
					$re = $this->User->changeUserAdat( $this->view->user['data']['ID'], $_POST );
					$this->view->msg['alapadat'] = Helper::makeAlertMsg('pSuccess',$re);
					Helper::reload('/user/beallitasok');
				}catch(Exception $e){
					$this->view->err = true;
					$this->view->msg['alapadat'] = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
			// Szállítási adatok cseréje
			if(Post::on('saveSzallitasi')){
				try{
					$re = $this->User->changeSzallitasiAdat( $this->view->user['data']['ID'], $_POST );
					$this->view->msg['szallitasi'] = Helper::makeAlertMsg('pSuccess',$re);
					Helper::reload('/user/beallitasok');
				}catch(Exception $e){
					$this->view->err = true;
					$this->view->msg['szallitasi'] = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
			// Számlázási adatok cseréje
			if(Post::on('saveSzamlazasi')){
				try{
					$re = $this->User->changeSzamlazasiAdat( $this->view->user['data']['ID'], $_POST );
					$this->view->msg['szamlazasi'] = Helper::makeAlertMsg('pSuccess',$re);
					Helper::reload('/user/beallitasok');
				}catch(Exception $e){
					$this->view->err = true;
					$this->view->msg['szamlazasi'] = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
		}

		function jelszocsere()
		{
			if ( !$this->view->user ) {
				Helper::reload('/user/belepes');
			}

			if( Post::on('changePassword') ) {
				try{
					$re = $this->User->changePassword( $this->view->user['data']['ID'], $_POST );
					Helper::reload('/user/jelszocsere?msg=Jelszavát sikeresen lecserélte. Mostantól új jelszavával tud bejelentkezni!');
				}catch(Exception $e){
					$err = $e->getCode();
					$this->view->msg = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
		}

		function jelszoemlekezteto()
		{
			if( Post::on('resetPassword') ) {
				try{
					$re = $this->User->resetPassword($_POST);
					Helper::reload('/user/jelszoemlekezteto?msg=E-mail címére elküldtük az új automatikusan generált jelszavát!');
				}catch(Exception $e){
					$err = $e->getCode();
					$this->view->msg = Helper::makeAlertMsg('pError',$e->getMessage());
					$this->out( 'err', $err );
				}
			}
		}

		function belepes(){

			if ( $this->view->user ) {
				Helper::reload('/user');
			}

			if( Post::on('loginUser') ) {
				$reurl = '/user';

				if ( $_GET['return'] ) {
					$reurl = $_GET['return'];
				}

				try{
					$re = $this->User->login($_POST);
					Helper::reload($reurl);
				}catch(Exception $e){
					$err = $e->getCode();
					$this->view->msg = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
		}

		function regisztracio(){
			if ( $this->view->user ) {
				Helper::reload('/user');
			}

			// Regisztráció
			if( Post::on('registerUser') ) {
				try{
					$re = $this->User->add($_POST);
					Helper::reload('/user/regisztracio?msg=Sikeresen rögzítettük regisztrációját! E-mail címére elküldök az aktiváló e-mailt. Ha nem találja beérkezett üzenetei közt, kérjük, hogy nézze meg levélszemét (SPAM) mappájában is!');
				}catch(Exception $e){
					$err = $e->getCode();
					$this->out( 'err', $err );
					$this->view->msg = Helper::makeAlertMsg('pError',$e->getMessage());
				}
			}
		}

		function logout(){
			unset($_SESSION[user_email]);

			Helper::reload($_SERVER[HTTP_REFERER]);
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
