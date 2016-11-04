<?
use ShopManager\Categories;
use ProductManager\Products;
use ProductManager\Product;
use PortalManager\Pagination;
use PortalManager\Template;
use PortalManager\Admin;
use FileManager\FileLister;
use Applications\XMLParser;

class termekek extends Controller
{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Termékek / Adminisztráció';

			$this->view->adm = $this->AdminUser;
			$this->view->adm->logged = $this->AdminUser->isLogged();

			if( $_GET['backmsg'] )
			{
				$this->view->bmsg= Helper::makeAlertMsg('pSuccess', $_GET['msg']);
			}

			// Kategóriák
			$cats = new Categories(  array( 'db' => $this->db )  );
			$this->out( 'categories', $cats->getTree() );

			if(Post::on('actionSaving')){
				try{
					$arg = array();
					$re = $this->AdminUser->doAction($_POST[selectAction], 'action_', $arg);
					/*echo '<pre>';
					print_r($_POST);
					echo '</pre>';*/
					$this->view->rmsg	= Helper::makeAlertMsg('pSuccess', $re);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->rmsg= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if(Post::on('filterList')){
				$filtered = false;

				if($_POST['ID'] != ''){
					setcookie('filter_ID',$_POST['ID'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_ID','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['cikkszam'] != ''){
					setcookie('filter_cikkszam',$_POST['cikkszam'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_cikkszam','',time()-100,'/'.$this->view->gets[0]);
				}


				if($_POST['szin'] != ''){
					setcookie('filter_szin',$_POST['szin'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_szin','',time()-100,'/'.$this->view->gets[0]);
				}


				if($_POST['meret'] != ''){
					setcookie('filter_meret',$_POST['meret'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_meret','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['nev'] != ''){
					setcookie('filter_nev',$_POST['nev'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_nev','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['marka'] != ''){
					setcookie('filter_marka',$_POST['marka'],time()+60*24,'/'.$this->view->gets[0]);
						$filtered = true;
				}else{
					setcookie('filter_marka','',time()-100,'/'.$this->view->gets[0]);
				}
				if($_POST['szallitasID'] != ''){
					setcookie('filter_szallitasID',$_POST['szallitasID'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_szallitasID','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['keszletID'] != ''){
					setcookie('filter_keszletID',$_POST['keszletID'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_keszletID','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['lathato'] != ''){
					setcookie('filter_lathato',$_POST['lathato'],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_lathato','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST['fotermek'] == 'on'){
					setcookie('filter_fotermek',1,time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_fotermek','',time()-100,'/'.$this->view->gets[0]);
				}

				if($filtered){
					setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
				}else{
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				}
				Helper::reload('/termekek/1');
			}

			// Termék lista
			$products = new Products( array( 'db' => $this->db ) );

			$filters = Helper::getCookieFilter('filter',array('filtered'));
			$arg = array(
				'admin' => true,
				'filters' => $filters,
				'limit' => 50,
				'page' => Helper::currentPageNum(),
				'order' => array(
					'by' => 'ID',
					'how' => 'DESC'
				)
			);
			$products_list = $products->prepareList( $arg )->getList();
			$this->out( 'products', $products );
			$this->out( 'termekek', $products_list );
			$this->out( 'navigator', (new Pagination(array(
				'class' => 'pagination pagination-sm center',
				'current' => $products->getCurrentPage(),
				'max' => $products->getMaxPage(),
				'root' => '/'.__CLASS__,
				'item_limit' => 28
			)))->render() );

			// Márkák
			$this->view->markak 	= $this->AdminUser->getMarkak();
			// Kategóriák
			$this->view->kategoria  = $this->AdminUser->getTermekKategoriak();

			// Készlet lista
			$this->view->keszlet 	= $this->AdminUser->getKeszletLista();
			// Szállításimód lista
			$this->view->szallitasMod 	= $this->AdminUser->getSzallitasModLista();
			// Szállításimód lista
			$fizmod = $this->AdminUser->getFizetesiModok();

			$nfizmod = array();
			foreach ( $fizmod as $fm ) {
				$nfizmod[$fm['ID']] = $fm;
			}
			$this->view->fizetesiMod = $nfizmod;
			// Szállítási idő lista
			$this->view->szallitas 	= $this->AdminUser->getSzallitasIdoLista();


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
			setcookie('filter_cikkszam','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_nev','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_szin','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_meret','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_lathato','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_fotermek','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_marka','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_szallitasID','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filter_keszletID','',time()-100,'/'.$this->view->gets[0]);
			setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
			Helper::reload('/termekek/');
		}

		function t(){

			$products = new Products( array( 'db' => $this->db ) );

			switch($this->view->gets[2]){
				case 'del':
					if(Post::on('delTermId')){
						$this->AdminUser->delTermek($this->view->gets[3]);
						Helper::reload('/termekek/-/1');
					}
				break;
				case 'imagesdelete':
					$imageset = $this->AdminUser->listTermekImages($this->view->gets[3]);
					$this->out('imageset', $imageset);

					if(Post::on('removeImagesOnTermek')){
						 $this->AdminUser->clearTermekImages($_POST);
						 Helper::reload('/termekek/t/edit/'.$this->view->gets[3]);
					}
				break;
				case 'edit': case 'newedit':

					// Termék másolása

					if(Post::on('copyTermek')){
						try{
							$re = $this->AdminUser->copyTermek($_POST[tid],$_POST[copyNum]);
							$this->view->copyMsg	= Helper::makeAlertMsg('pSuccess', $re);
						}catch(Exception $e){
							$this->view->err 	= true;
							$this->view->copyMsg= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}

					// Termék alapadatok szerkesztése
					if(Post::on('saveTermek')){
						try{
							// Termék adatok mentése
							$save = $products->save( (new Product( array( 'db' => $this->db ) ))
								->setId( $this->view->gets[3] )
								->setData( array(
								'cikkszam' => $_POST['cikkszam'],
								'nev' => $_POST['nev'],
								'marka' => $_POST['marka'],
								'rovid_leiras' => $_POST['rovid_leiras'],
								'leiras' => $_POST['leiras'],
								'szallitas' => $_POST['szallitasID'],
								'allapot' => $_POST['keszletID'],
								'akcios' => ($_POST['akcios'] ? 1 : 0),
								'ujdonsag' => ($_POST['ujdonsag'] ? 1 : 0),
								'arukereso' => ($_POST['argep'] ? 1 : 0 ),
								'argep' => ($_POST['argep'] ? 1 : 0 ),
								'pickpackpont' => ($_POST['pickpackszallitas'] ? 1 : 0 ),
								'garancia' => ($_POST['garancia'] ?: 0),
								'cat' => $_POST['cat'],
								'szin' => $_POST['szin'],
								'meret' => $_POST['meret'],
								'fotermek' => ($_POST['fotermek'] == 'on' ? 1 : 0),
								'lathato' => ($_POST['lathato'] == 'on' ? 1 : 0),
								'ar_netto' => $_POST['netto_ar'],
								'ar_brutto' => $_POST['brutto_ar'],
								'ar_akcios_netto' => $_POST['akcios_netto_ar'],
								'ar_akcios_brutto' => $_POST['akcios_brutto_ar'],
								'raktar_keszlet' => $_POST['raktar_keszlet'],
								'linkek' => array(
									'nev' => $_POST['linkNev'],
									'url' => $_POST['linkUrl'],
								),
								'ar_by' => $_POST['ar_by'],
								'akcios_ar_by' => $_POST['akcios_ar_by'],
								'alapertelmezett_kategoria' => $_POST['alapertelmezett_kategoria'],
								'kulcsszavak' => $_POST['kulcsszavak'],
								'raktar_articleid' => $_POST['raktar_articleid'],
								'raktar_variantid' => $_POST['raktar_variantid'],
								'raktar_supplierid' => $_POST['raktar_supplierid'],
								'raktar_number' => $_POST['raktar_number']
							) ) );
							Helper::reload( '/termekek/t/edit/'.$this->view->gets[3].'/?backmsg=success&msg='.$save);
						} catch (Exception $e){
							$this->view->err 	= true;
							$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
						}
					}

					// Termék adatainak lekérése
					$this->out( 'products', $products );
					$this->out( 'termek', $products->get( $this->view->gets[3] ) );
					if ( $this->view->termek['hasonlo_termek_ids']['ids'] ) {
						$termek_kapcsolatok = new Products( array( 'db' => $this->db ) );
						$termek_kapcsolatok = $termek_kapcsolatok->prepareList( array(
							'limit' => -1,
							'admin' => true,
							'filters' => array(
								'ID' => $this->view->termek['hasonlo_termek_ids']['ids']
							)
						) )->getList();
						$kapcsolat_template = new Template( VIEW  . 'templates/' );
						$this->out(
							'kapcsolatok',
							$kapcsolat_template->get(
								'products_relatives',
								array(
									'list' 	=> $termek_kapcsolatok,
									'mode' 	=> 'remove',
									'id' 	=> $this->view->termek['ID']
								)
							)
						);
					}

					// Termék képek feltöltése
					if(Post::on('uploadImg')){
						$folder 	= '';
						$firstImg 	= false;
						$tid 		= $_POST[tid];

						$folder 	= ( $_POST['dir'] ) ?: 'src/products/p'.$this->view->gets[3];
						$firstImg 	= true;

						if(!file_exists($folder)){
							mkdir($folder,0777,true);
						}

						$tdata = $this->view->termek;

						try{
							$mt = explode(" ",str_replace(".","",microtime()));
							$imgName = Helper::makeSafeUrl($products->getManufacturName($tdata[marka]).'-'.$tdata[nev], '__'.date('YmdHis').$mt[0]);

							$rei = Images::upload(array(
								'src' 		=> 'img',
								'noRoot' 	=> true,
								'upDir' 	=> $folder,
								'fileName' 	=> $imgName,
								'maxFileSize' => 5150
							));
						}catch(Exception $e){
							echo $e->getMessage();
						}

						$upDir 		= $rei['dir'];
						$upProfil 	= $rei['file'];

						if ( count($rei['allUploadedFiles']) > 0 ) {
							$img = $this->db->query("SELECT profil_kep FROM shop_termekek WHERE ID = $tid;")->fetch(PDO::FETCH_ASSOC);

							if( $img['profil_kep'] == '' || !$img['profil_kep'] )
								if( $firstImg ) {
									$this->model->db->query("UPDATE shop_termekek SET kep_mappa = '{$upDir}', profil_kep = '$upProfil' WHERE ID = $tid;");
								}
						}

						if ( count($rei['allUploadedFiles']) > 0 ) {
							foreach($rei['allUploadedFiles'] as $kep){
								$products->addImageToProduct( $tid, $kep );
							}
						}

						Helper::reload();
					}

					// Kategóriák
					$cats = new Categories( array( 'db' => $this->db ) );
					$this->out( 'categories', $cats->getTree() );
					// Márka lista
					$this->view->markak 		= $this->AdminUser->getMarkak();
					// Készlet lista
					$this->view->keszlet 	= $this->AdminUser->getKeszletLista();
					// Szállítási idő lista
					$this->view->szallitas 	= $this->AdminUser->getSzallitasIdoLista();

				break;
				case 'delListingFromKat':
					if(Post::on('delKatItemID')){
						$this->model->db->query("DELETE FROM shop_termek_in_kategoria WHERE ID = {$_POST[delKatItemID]}");
						Helper::reload('/termekek/t/edit/'.$this->view->gets[4]);
					}
				break;
			}
		}

		function upload_image()
		{

			$products = new Products( array( 'db' => $this->db ) );

			$ids = explode("|", $this->view->gets[2]);
			$this->out( 'ids', $ids );


			// Termék képek feltöltése
			if(Post::on('upload')){
				$folder 	= '';


				$folder 	= ( $_POST['dir'] ) ?: 'src/products/upload_'.date('Ymd');
				$firstImg 	= true;

				if(!file_exists($folder)){
					mkdir($folder,0777,true);
				}

				$tdata = $this->view->termek;

				try{
					$mt = explode(" ",str_replace(".","",microtime()));
					$imgName = 'productimg__'.date('YmdHis').$mt[0];

					$rei = Images::upload(array(
						'src' 		=> 'img',
						'noRoot' 	=> true,
						'upDir' 	=> $folder,
						'fileName' 	=> $imgName,
						'maxFileSize' => 5150
					));
				}catch(Exception $e){
					echo $e->getMessage();
				}

				$upDir 		= $rei['dir'];
				$upProfil 	= $rei['file'];


				if ( count($rei['allUploadedFiles']) > 0 ) {
					foreach ( $ids as $sid ) {
						$firstImg 	= true;

				  		$products->removeImageFromProduct( $sid );
				  		foreach( $rei['allUploadedFiles'] as $kep ){
				  			if ( $firstImg ) {
				  				$products->setProfilImageToProduct( $sid, $kep );
				  				$firstImg = false;
				  			}
							$products->addImageToProduct( $sid, $kep );
						}
				  	}
				}

				Helper::reload('/termekek/upload_image/?msgkey=msg&msg=Képfeltöltés sikeres. A kiválasztott termékek képei frissítve lettek!');
			}

		}

		function szallitasi_mod(){

			if(Post::on('add')){
				try{
					$this->AdminUser->addSzallitasMod($_POST);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('save')){
				try{
					$this->AdminUser->saveSzallitasMod($_POST);
					Helper::reload('/termekek/szallitasi_mod/?msgkey=msg&msg=Változások sikeresen mentve lettek!');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('delId')){
				try{
					$this->AdminUser->delSzallitasMod($this->view->gets[3]);
					Helper::reload('/termekek/szallitasi_mod');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if($this->view->gets[2] == 'szerkeszt'){
				$this->view->sm = $this->AdminUser->getSzallitasModData($this->view->gets[3]);
			}


			$this->view->n = $this->AdminUser->getSzallitasiModok();
		}

		function fizetesi_mod(){

			if(Post::on('add')){
				try{
					$this->AdminUser->addFizetesiMod($_POST);
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('save')){
				try{
					$this->AdminUser->saveFizetesiMod($_POST);
					Helper::reload('/termekek/fizetesi_mod');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('delId')){
				try{
					$this->AdminUser->delFizetesiMod($this->view->gets[3]);
					Helper::reload('/termekek/fizetesi_mod');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if($this->view->gets[2] == 'szerkeszt'){
				$this->view->sm = $this->AdminUser->getSzallitasModData($this->view->gets[3]);
			}


			$this->view->n = $this->AdminUser->getFizetesiModok();
		}

		function szallitasi_ido(){
			if(Post::on('add')){
				try{
					$this->AdminUser->addSzallitasIdo($_POST);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('save')){
				try{
					$this->AdminUser->saveSzallitasIdo($_POST);
					Helper::reload('/termekek/szallitasi_ido');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			if(Post::on('delId')){
				try{
					$this->AdminUser->delSzallitasIdo($this->view->gets[3]);
					Helper::reload('/termekek/szallitasi_ido');
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->emsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if($this->view->gets[2] == 'szerkeszt'){
				$this->view->sm = $this->AdminUser->getSzallitasIdoData($this->view->gets[3]);
			}

			$this->view->n = $this->AdminUser->getSzallitasIdoList();
		}

		function uj(){
			// Termék márkák
			$this->view->markak = $this->AdminUser->getMarkak();
			// Termékek
			$products = new Products( array( 'db' => $this->db ) );

			// Kategória lista
			$categories = new Categories( false, array( 'db' => $this->db ) );
			$cat_tree 	= $categories->getTree();
			$this->out( 'categories', $cat_tree );

			// ÚJ termék
			if(Post::on('ujTermek')){
				try{
					//$re = $this->AdminUser->addTermek($_POST);
					$re = $products->create( (new Product( array( 'db' => $this->db ) ))->setData( array(
						'cikkszam' => $_POST['nagyker_kod'],
						'nev' => $_POST['nev'],
						'marka' => $_POST['marka'],
						'rovid_leiras' => $_POST['rovid_leiras'],
						'leiras' => $_POST['leiras'],
						'szallitas' => $_POST['szallitasID'],
						'allapot' => $_POST['keszletID'],
						'akcios' => ($_POST['akcios'] ? 1 : 0),
						'ujdonsag' => ($_POST['ujdonsag'] ? 1 : 0),
						'arukereso' => ($_POST['argep'] ? 1 : 0 ),
						'argep' => ($_POST['argep'] ? 1 : 0 ),
						'pickpackpont' => ($_POST['pickpackszallitas'] ? 1 : 0 ),
						'garancia' => ($_POST['garancia'] ?: 0),
						'cat' => $_POST['cat'],
						'ar' => $_POST['ar'],
						'ar_netto' => $_POST['netto_ar'],
						'ar_brutto' => $_POST['brutto_ar'],
						'ar_akcios' => $_POST['akcios_ar'],
						'ar_akcios_netto' => $_POST['akcios_netto_ar'],
						'ar_akcios_brutto' => $_POST['akcios_brutto_ar'],
						'szin' => $_POST['szin'],
						'meret' => $_POST['meret'],
						'fotermek' => ($_POST['fotermek'] == 1 ? 1 : 0),
						'kulcsszavak' => $_POST['kulcsszavak'],
						'linkek' => array(
							'nev' => $_POST['linkNev'],
							'url' => $_POST['linkUrl'],
						),
						'connects' => $_POST['productConnects']
					) ) );

					Helper::reload( '/termekek/uj/?backmsg=success&msg='.$re);
				}catch(Exception $e){
					$this->view->err 	= true;
					$this->view->bmsg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

		}

		function kepek()
		{
			$this->Admin = new Admin( false, array( 'db' => $this->db )  );

			if( Post::on('connectImagase') ){
				try {
					$this->Admin->autoProductImageConnecter();
					Helper::reload( '/termekek/kepek/?backmsg=success&msg=Képek frissítése megtörtént' );
				} catch (Exception $e) {
					$this->view->err 	= true;
					$this->view->msg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
		}

		function import()
		{
			$this->Admin 			= new Admin( false, array( 'db' => $this->db )  );
			$this->uploaded_files 	= new FileLister( 'src/uploaded_files/' );

			// Online terméklista xml linkjének mentése
			if( Post::on('save_online_xml_url') ){
				try {
					$re = $this->Admin->save_product_xml_url( $_POST['import_online_xml'] );
					Helper::reload();
				} catch (Exception $e) {
					$this->view->err 	= true;
					$this->view->msg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if( Post::on('upload_xml') ) {
				if ( $_FILES['xml']['error'] == UPLOAD_ERR_OK ) {
					move_uploaded_file( $_FILES['xml']['tmp_name'], 'src/uploaded_files/'.$_FILES['xml']['name']);
					Helper::reload();
				}
			}

			if ( $_GET['checkurl']) {
				$header = @get_headers( $_GET['checkurl'], 1 );
				$header['header_code'] = substr( $header[0], 9, 3 );

				$this->out( 'url_result',  $header );
			}

			if ( $this->view->gets[2] == 'preview' ) {
				// Lista Típusa
				$ext = pathinfo( $_GET['file'], PATHINFO_EXTENSION );

				switch( $ext ) {
					case 'json':
						$json_result = json_decode( file_get_contents( $_GET['file'] ) );

						$articles = $json_result->parameters;

						/**
						 * JSON ELŐKÉSZÍTÉS
						 **/
						$repaired_articles = array();
						foreach ( $articles as $article ) {
							foreach ( $article->variants as $variant ) {
								$in = array();

								$in[articleid] = $article->article->articleid;
								$in[name] = $article->article->name;
								$in[number] = $article->article->number;
								$in[supplier_articlenumber] = $article->article->supplier_articlenumber;
								$in[description] = $article->article->description;
								$in[variantid] = $variant->variantid;
								$in[color_number] = $variant->color_number;
								$in[color_name] = $variant->color_name;
								$in[size_name] = $variant->size;
								$in[netprice] = $variant->netprice;
								$in[grossprice] = $variant->grossprice;

								$cats = array();
								if( count( $article->categories ) > 0){
									foreach ( $article->categories as $cat ) {
										$cats[] = $cat->category;
									}
								}
								$in[categories] = $cats;

								$repaired_articles[] = $in;
							}
						}

						$this->out( 'xml_result',  $repaired_articles );
						$this->out( 'xml_import_check', $this->Admin->checkImportProducts( $this->view->xml_result ) );

					break;
					case 'xml':
						$this->XMList = new XMLParser( $_GET['file'] );
						$this->out( 'xml_result',  $this->XMList->getResult() );
						$this->out( 'xml_import_check', $this->Admin->checkImportProducts( $this->view->xml_result ) );
					break;

				}
			}

			$this->out( 'uploaded_files', $this->uploaded_files );

			// Import művelet
			if ( Post::on('action_do') ) {
				try {
					// Új termékek importálása
					if ( Post::on('create_new_products') ) {
						$this->Admin->importProducts( $this->view->xml_import_check, array( 'mode' => 'create' ) );
					}
					// Frissítendő termékek frissítése
					if ( Post::on('only_update_products') ) {
						$this->Admin->importProducts( $this->view->xml_import_check, array( 'mode' => 'update' ) );
					}

					//Helper::reload('/termekek/import/?msgkey=msg&msg=Importálás műveletek sikeresen befejeződtek!');

				} catch ( Exception $e ) {
					$this->view->err 	= true;
					$this->view->msg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}

			}
		}

		function nagyker_arlista(){

			if( Post::on('uploadList') ){
				try {
					$re = $this->AdminUser->uploadNagykerLista();
					Helper::reload();
				} catch (Exception $e) {
					$this->view->err 	= true;
					$this->view->bmsg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if( Post::on('addNagyker') ){
				try {
					$re = $this->AdminUser->addNagyker($_POST);
					Helper::reload();
				} catch (Exception $e) {
					$this->view->err 	= true;
					$this->view->bmsg	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Nagyker árlista
			$this->view->arlista = $this->AdminUser->getNagykerArlista();

			if( $_GET[path] != '' )
			$this->view->csv = CSVParser::GET( $_GET[path] );
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
