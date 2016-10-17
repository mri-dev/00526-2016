<? 
use PortalManager\Admin;

class megrendelesek extends Controller{
		function __construct(){	
			parent::__construct();
			parent::$pageTitle = 'Megrendelések / Adminisztráció';

			$this->Admin = new Admin( false, array( 'db' => $this->db, 'view' => $this->view ) );
						
			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();
					
			if(Post::on('filterList')){
				$filtered = false;
				
				if($_POST[ID] != ''){
					setcookie('filter_ID',$_POST[ID],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[azonosito] != ''){
					setcookie('filter_azonosito',$_POST[azonosito],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_azonosito','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[nameemail] != ''){
					setcookie('filter_nameemail',$_POST[nameemail],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_nameemail','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[fallapot] != ''){
					setcookie('filter_fallapot',$_POST[fallapot],time()+60*24,'/'.$this->view->gets[0]);
						$filtered = true;
				}else{
					setcookie('filter_fallapot','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[fszallitas] != ''){
					setcookie('filter_fszallitas',$_POST[fszallitas],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_fszallitas','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[ffizetes] != ''){
					setcookie('filter_ffizetes',$_POST[ffizetes],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_ffizetes','',time()-100,'/'.$this->view->gets[0]);
				}
				
				if($filtered){
					setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
				}else{
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				}				
				Helper::reload( '/megrendelesek/-/1' );
			}
									
			if(Post::on('saveOrder'))
			{	
				try{
					$this->view->chg = $this->Admin->saveOrderData($_POST[saveOrder],$_POST);
					Helper::reload('/'.__CLASS__);
				}catch(Exception $e){
					$this->view->err = true;
					$this->out( 'msg', Helper::makeAlertMsg( 'pError', $e->getMessage() ) );
				}
			}
					
			$this->view->fizetes 		= $this->AdminUser->getFizetesiModok();
			$this->view->szallitas 		= $this->AdminUser->getSzallitasiModok();
			$this->view->allapotok[order]= $this->AdminUser->getMegrendelesAllapotok();
			$this->view->allapotok[termek]= $this->AdminUser->getMegrendeltTermekAllapotok();
			
			$arg 			= array();
			$arg[limit] 	= 50;
			$filters 		= Helper::getCookieFilter('filter',array('filtered'));
			$arg[filters] 	= $filters;
			if($_GET[ID]){
				$arg[filters][ID] = $_GET[ID];	
			}
			$this->view->megrendelesek = $this->AdminUser->getMegrendelesek($arg);
			
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
		
		function clearfilters(){
			setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_azonosito','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_nameemail','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_fallapot','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_fszallitas','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_ffizetes','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
			Helper::reload('/'.$this->view->gets[0]);
		}
		
		function allapotok(){
			if(Post::on('save')){
				try{
					$this->AdminUser->saveMegrendelesAllapot($_POST);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			if(Post::on('add')){
				try{
					$this->AdminUser->addMegrendelesAllapot($_POST);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			if(Post::on('delId')){
				try{
					$this->AdminUser->delMegrendelesAllapot($this->view->gets[3]);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
					
			$this->view->o = $this->AdminUser->getMegrendelesAllapotok();
			$this->view->sm = $this->view->o[Helper::getFromArrByAssocVal($this->view->o,'ID',$this->view->gets[3])];
		}
		
		function termek_allapotok(){
			if(Post::on('save')){
				try{
					$this->AdminUser->saveMegrendelesTermekAllapot($_POST);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			if(Post::on('add')){
				try{
					$this->AdminUser->addMegrendelesTermekAllapot($_POST);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			
			if(Post::on('delId')){
				try{
					$this->AdminUser->delMegrendelesTermekAllapot($this->view->gets[3]);	
					Helper::reload('/'.__CLASS__.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
					
			$this->view->o = $this->AdminUser->getMegrendeltTermekAllapotok();
			$this->view->sm = $this->view->o[Helper::getFromArrByAssocVal($this->view->o,'ID',$this->view->gets[3])];
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>