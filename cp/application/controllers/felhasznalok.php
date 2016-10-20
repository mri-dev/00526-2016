<? 
class felhasznalok extends Controller{
		function __construct(){	
			parent::__construct( array( 'admin' => true ));
			parent::$pageTitle = 'Felhasználók';
			
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
				if($_POST[engedelyezve] != ''){
					setcookie('filter_engedelyezve',$_POST[engedelyezve],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_engedelyezve','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST[aktivalva] != ''){
					setcookie('filter_aktivalva',$_POST[aktivalva],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_aktivalva','',time()-100,'/'.$this->view->gets[0]);
				}
				
				
				if($filtered){
					setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
				}else{
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				}				
				Helper::reload();
			}
			
			$arg = array();
			$arg[limit] = 50;
			$filters = Helper::getCookieFilter('filter',array('filtered'));
			$arg[filters] = $filters;
			
			$this->view->users = $this->User->getUserList($arg);


							
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
		
		function clearfilters(){
			setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_nev','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_email','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_engedelyezve','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_aktivalva','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
			Helper::reload('/'.$this->view->gets[0]);
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>